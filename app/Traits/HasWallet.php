<?php

namespace App\Traits;

trait HasWallet
{
    /**
     * Menambahkan jumlah ke saldo dompet pengguna
     *
     * @param float $amount
     * @return bool
     */
    public function deposit($amount)
    {
        if ($amount <= 0) {
            return false;
        }

        $this->balance += $amount;
        return $this->save();
    }

    /**
     * Menarik jumlah dari saldo dompet pengguna
     *
     * @param float $amount
     * @return bool
     */
    public function withdraw($amount)
    {
        if ($amount <= 0) {
            return false;
        }

        if ($this->balance < $amount) {
            return false;
        }

        $this->balance -= $amount;
        return $this->save();
    }

    /**
     * Mendapatkan saldo terformat dengan simbol mata uang
     *
     * @return string
     */
    public function getFormattedBalanceAttribute()
    {
        return 'Rp ' . number_format($this->balance, 0, ',', '.');
    }

    /**
     * Memeriksa apakah pengguna memiliki saldo yang cukup
     *
     * @param float $amount
     * @return bool
     */
    public function hasEnoughBalance($amount)
    {
        return $this->balance >= $amount;
    }
}