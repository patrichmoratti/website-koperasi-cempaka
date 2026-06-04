<?php

namespace App\Services;

use App\Models\KoperasiInfo;
use App\Models\TransaksiGadai;
use App\Models\Simpanan;
use App\Models\BiayaOperasional;
use App\Models\ShuDistribution;
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
        $info       = KoperasiInfo::getInstance();
        $query      = PembayaranGadai::with('transaksi')->where('status', 'confirmed')->whereYear('confirmed_at', $year);
        $biayaQuery = BiayaOperasional::whereYear('date', $year);

        if ($month) {
            $query->whereMonth('confirmed_at', $month);
            $biayaQuery->whereMonth('date', $month);
        }

        $pendapatan = $query->get();
        $biaya      = $biayaQuery->get();
        $totalIn    = $pendapatan->sum('amount');
        $totalOut   = $biaya->sum('amount');

        $pdf = Pdf::loadView('reports.keuangan-pdf', compact('info', 'pendapatan', 'biaya', 'totalIn', 'totalOut', 'year', 'month'));
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

    public function shuPdf(int $periodId): Response
    {
        $info          = KoperasiInfo::getInstance();
        $distributions = ShuDistribution::with(['anggota', 'period'])->where('shu_period_id', $periodId)->get();
        $period        = $distributions->first()?->period;
        $pdf           = Pdf::loadView('reports.shu-pdf', compact('info', 'distributions', 'period'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download("distribusi-shu-{$period?->year}.pdf");
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

    public function simpananExcel(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $data = Simpanan::with('anggota')->latest()->get()->map(fn($s) => [
            $s->anggota->name,
            $s->type_label,
            $s->period_label,
            number_format($s->amount, 0, ',', '.'),
            $s->status,
            $s->confirmed_at?->format('d/m/Y') ?? '-',
        ])->toArray();

        return Excel::download(new class($data) implements FromArray, WithHeadings, WithTitle {
            public function __construct(private array $data) {}
            public function array(): array { return $this->data; }
            public function headings(): array {
                return ['Anggota','Tipe','Periode','Jumlah','Status','Tgl Konfirmasi'];
            }
            public function title(): string { return 'Data Simpanan'; }
        }, 'data-simpanan.xlsx');
    }

    public function shuExcel(int $periodId): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $data = ShuDistribution::with(['anggota','period'])->where('shu_period_id', $periodId)->get()->map(fn($d) => [
            $d->anggota->name,
            number_format($d->total_savings, 0, ',', '.'),
            number_format($d->member_jasa_modal, 0, ',', '.'),
            number_format($d->total_interest_paid, 0, ',', '.'),
            number_format($d->member_jasa_usaha, 0, ',', '.'),
            number_format($d->total_shu_received, 0, ',', '.'),
            $d->withdrawal_status === 'withdrawn' ? 'Sudah Dicairkan' : 'Belum Dicairkan',
        ])->toArray();

        return Excel::download(new class($data) implements FromArray, WithHeadings, WithTitle {
            public function __construct(private array $data) {}
            public function array(): array { return $this->data; }
            public function headings(): array {
                return ['Anggota','Total Simpanan','Jasa Modal','Total Bunga Dibayar','Jasa Usaha','Total SHU','Status'];
            }
            public function title(): string { return 'Distribusi SHU'; }
        }, 'distribusi-shu.xlsx');
    }
}
