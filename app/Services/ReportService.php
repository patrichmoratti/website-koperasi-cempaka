<?php

namespace App\Services;

use App\Models\KoperasiInfo;
use App\Models\TransaksiGadai;
use App\Models\Simpanan;
use App\Models\PengajuanGadai;
use App\Models\PembayaranGadai;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Http\Response;

class ReportService
{
    public function keuanganPdf(int $year, ?int $month = null): Response
    {
        $info           = KoperasiInfo::getInstance();
        $query          = PembayaranGadai::with('transaksi.anggota')->where('status', 'confirmed')->whereYear('confirmed_at', $year);
        $simpananQuery  = Simpanan::confirmed()->whereYear('confirmed_at', $year);
        $pengajuanQuery = PengajuanGadai::with(['anggota', 'jenisBarang'])->whereYear('submitted_at', $year);

        if ($month) {
            $query->whereMonth('confirmed_at', $month);
            $simpananQuery->whereMonth('confirmed_at', $month);
            $pengajuanQuery->whereMonth('submitted_at', $month);
        }

        $pendapatan    = $query->get();
        $totalIn       = $pendapatan->sum('amount');
        $totalSimpanan = $simpananQuery->sum('amount');
        $pengajuan     = $pengajuanQuery->latest('submitted_at')->get();

        $pdf = Pdf::loadView('reports.keuangan-pdf', compact('info', 'pendapatan', 'totalIn', 'totalSimpanan', 'pengajuan', 'year', 'month'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download("laporan-keuangan-{$year}.pdf");
    }

    public function gadaiPdf(TransaksiGadai $transaksi): Response
    {
        $info = KoperasiInfo::getInstance();
        $pdf  = Pdf::loadView('reports.gadai-detail-pdf', compact('info', 'transaksi'));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download("transaksi-{$transaksi->reference_number}.pdf");
    }

    public function gadaiExcel(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $data = TransaksiGadai::with(['anggota', 'jenisBarang'])->latest()->get()->map(fn($t) => [
            $t->reference_number,
            $t->anggota->name,
            $t->jenisBarang->name,
            $t->item_description,
            number_format($t->loan_amount, 0, ',', '.'),
            number_format($t->appraisal_value, 0, ',', '.'),
            $t->pawn_date->format('d/m/Y'),
            $t->due_date->format('d/m/Y'),
            $t->status_label,
        ])->toArray();

        return Excel::download(new class($data) implements FromArray, WithHeadings, WithTitle {
            public function __construct(private array $data) {}
            public function array(): array { return $this->data; }
            public function headings(): array {
                return ['No. Referensi','Anggota','Jenis Barang','Deskripsi','Pinjaman','Nilai Taksir','Tgl Gadai','Jatuh Tempo','Status'];
            }
            public function title(): string { return 'Transaksi Gadai'; }
        }, 'data-gadai.xlsx');
    }

    public function pembayaranExcel(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $simpanan = Simpanan::with(['anggota', 'confirmedBy'])->confirmed()->get()->map(fn($s) => [
            $s->anggota?->name ?? '-',
            $s->type_label,
            'Periode ' . $s->period_label,
            number_format($s->amount, 0, ',', '.'),
            $s->confirmedBy?->name ?? '-',
            $s->confirmed_at?->format('d/m/Y') ?? '-',
        ]);

        $gadai = PembayaranGadai::with(['transaksi.anggota', 'transaksi.jenisBarang', 'confirmedBy'])->confirmed()->get()->map(fn($p) => [
            $p->transaksi?->anggota?->name ?? '-',
            'Gadai - ' . $p->payment_type_label,
            ($p->transaksi?->jenisBarang?->name ?? '-') . ' · ' . ($p->transaksi?->reference_number ?? '-'),
            number_format($p->amount, 0, ',', '.'),
            $p->confirmedBy?->name ?? '-',
            $p->confirmed_at?->format('d/m/Y') ?? '-',
        ]);

        $data = $simpanan->concat($gadai)->toArray();

        return Excel::download(new class($data) implements FromArray, WithHeadings, WithTitle {
            public function __construct(private array $data) {}
            public function array(): array { return $this->data; }
            public function headings(): array {
                return ['Anggota','Jenis','Keterangan','Jumlah','Disetujui Oleh','Tgl Disetujui'];
            }
            public function title(): string { return 'Data Pembayaran'; }
        }, 'data-pembayaran.xlsx');
    }
}
