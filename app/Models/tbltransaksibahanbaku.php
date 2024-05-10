<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class tbltransaksibahanbaku extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'tbltransaksibahanbaku';
    protected $primaryKey = 'ID_Transaksi_Baku';
    protected $fillable = [
        "Tanggal",
    ];

    public function bahanbaku() : BelongsToMany
    {
        return $this->belongsToMany(tblbahanbaku::class, 'tbldetailtransaksibahanbaku', 'ID_transaksi_Baku', 'ID_Bahan_Baku')
            ->withPivot('Kuantitas', 'Sub_Total');
    }
}
