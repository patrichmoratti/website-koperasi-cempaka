<?php

namespace App\Services;

use App\Models\ShuPeriod;
use App\Models\ShuDistribution;
use App\Models\User;
use App\Models\Simpanan;
use App\Models\PembayaranGadai;
use App\Models\TransaksiGadai;
use App\Models\BiayaOperasional;
use Illuminate\Support\Facades\DB;

class ShuCalculatorService
{
    public function calculate(ShuPeriod $period): ShuPeriod
    {
        return DB::transaction(function () use ($period) {
            $year = $period->year;

            $totalIncome = PembayaranGadai::whereHas('transaksi', fn($q) => $q->whereYear('pawn_date', $year))
                ->where('status', 'confirmed')
                ->sum('amount');

            $totalExpenses = BiayaOperasional::whereYear('date', $year)->sum('amount');

            $totalShu = max(0, $totalIncome - $totalExpenses);

            $allocJasaModal   = $totalShu * ($period->pct_jasa_modal / 100);
            $allocJasaUsaha   = $totalShu * ($period->pct_jasa_usaha / 100);
            $allocDanaCadangan    = $totalShu * ($period->pct_dana_cadangan / 100);
            $allocDanaPengurus    = $totalShu * ($period->pct_dana_pengurus / 100);
            $allocDanaPendidikan  = $totalShu * ($period->pct_dana_pendidikan / 100);
            $allocDanaSosial      = $totalShu * ($period->pct_dana_sosial / 100);

            $period->update([
                'total_income'         => $totalIncome,
                'total_expenses'       => $totalExpenses,
                'total_shu'            => $totalShu,
                'alloc_jasa_modal'     => $allocJasaModal,
                'alloc_jasa_usaha'     => $allocJasaUsaha,
                'alloc_dana_cadangan'  => $allocDanaCadangan,
                'alloc_dana_pengurus'  => $allocDanaPengurus,
                'alloc_dana_pendidikan'=> $allocDanaPendidikan,
                'alloc_dana_sosial'    => $allocDanaSosial,
                'status'               => 'closed',
            ]);

            $totalSimpananAll = (float) Simpanan::where('status', 'confirmed')
                ->whereYear('confirmed_at', $year)
                ->sum('amount');

            $totalInterestAll = (float) PembayaranGadai::where('payment_type', 'bunga')
                ->where('status', 'confirmed')
                ->whereYear('confirmed_at', $year)
                ->sum('amount');

            ShuDistribution::where('shu_period_id', $period->id)->delete();

            $anggotaList = User::where('role', 'anggota')->where('account_status', 'active')->get();
            $distributions = [];

            foreach ($anggotaList as $anggota) {
                $memberSavings = (float) Simpanan::where('anggota_id', $anggota->id)
                    ->where('status', 'confirmed')
                    ->whereYear('confirmed_at', $year)
                    ->sum('amount');

                $memberInterest = (float) PembayaranGadai::where('payment_type', 'bunga')
                    ->where('status', 'confirmed')
                    ->whereYear('confirmed_at', $year)
                    ->whereHas('transaksi', fn($q) => $q->where('anggota_id', $anggota->id))
                    ->sum('amount');

                $savingsProportion  = $totalSimpananAll > 0 ? $memberSavings / $totalSimpananAll : 0;
                $interestProportion = $totalInterestAll > 0 ? $memberInterest / $totalInterestAll : 0;
                $jasaModal  = $allocJasaModal * $savingsProportion;
                $jasaUsaha  = $allocJasaUsaha * $interestProportion;
                $totalShuMember = $jasaModal + $jasaUsaha;

                if ($totalShuMember > 0 || $memberSavings > 0) {
                    $distributions[] = [
                        'shu_period_id'              => $period->id,
                        'anggota_id'                 => $anggota->id,
                        'total_savings'              => $memberSavings,
                        'member_savings_proportion'  => $savingsProportion,
                        'member_jasa_modal'          => $jasaModal,
                        'total_interest_paid'        => $memberInterest,
                        'member_interest_proportion' => $interestProportion,
                        'member_jasa_usaha'          => $jasaUsaha,
                        'total_shu_received'         => $totalShuMember,
                        'withdrawal_status'          => 'pending',
                        'created_at'                 => now(),
                        'updated_at'                 => now(),
                    ];
                }
            }

            if (!empty($distributions)) {
                ShuDistribution::insert($distributions);
            }

            return $period->fresh();
        });
    }

    public function publish(ShuPeriod $period): void
    {
        $period->update(['status' => 'published']);
    }
}
