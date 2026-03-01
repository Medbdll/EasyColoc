<?php

namespace App\Services;

class BalanceCalculationService
{
    public function calculateMemberBalances($colocation)
    {
        $memberBalances = [];
        
        // Refresh the users relationship to ensure we have the latest data
        $colocation->load('users');
        
        foreach($colocation->users as $member) {
            $totalPaid = $colocation->expenses()->where('payer_id', $member->id)->sum('amount');
            $totalOwed = 0;
            
            foreach($colocation->expenses as $expense) {
                foreach($expense->participants as $participant) {
                    if ($participant->id == $member->id) {
                        $totalOwed += $participant->pivot->share_amount;
                    }
                }
            }
            
            // Calculate payments made and received
            $paymentsMade = $colocation->payments()->where('payer_id', $member->id)->sum('amount');
            $paymentsReceived = $colocation->payments()->where('receiver_id', $member->id)->sum('amount');
            
            $memberBalances[$member->id] = [
                'name' => $member->name,
                'balance' => ($totalPaid + $paymentsReceived) - ($totalOwed + $paymentsMade),
                'total_paid' => $totalPaid,
                'total_owed' => $totalOwed,
                'payments_made' => $paymentsMade,
                'payments_received' => $paymentsReceived
            ];
        }
        
        return $memberBalances;
    }
    
    public function calculateRepayments($memberBalances)
    {
        $debtors = [];
        $creditors = [];
        
        foreach($memberBalances as $memberId => $balance) {
            if ($balance['balance'] < 0) {
                $debtors[$memberId] = $balance;
            } elseif ($balance['balance'] > 0) {
                $creditors[$memberId] = $balance;
            }
        }
        
        $repayments = [];
        foreach($debtors as $debtorId => $debtor) {
            $remainingDebt = abs($debtor['balance']);
            foreach($creditors as $creditorId => $creditor) {
                if ($remainingDebt > 0.01 && $creditor['balance'] > 0.01) {
                    $paymentAmount = min($remainingDebt, $creditor['balance']);
                    $repayments[] = [
                        'from' => $debtor['name'],
                        'to' => $creditor['name'],
                        'amount' => $paymentAmount
                    ];
                    $remainingDebt -= $paymentAmount;
                }
            }
        }
        
        return $repayments;
    }
}
