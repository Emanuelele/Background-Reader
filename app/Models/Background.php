<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Background extends Model {
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'generality',
        'link',
        'type',
        'discord_id',
        'note'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [];

    /**
     * The rules for validating input.
     *
     * @var array<string, string>
     */
    public static $rules = [
        'generality' => 'required|string',
        'link' => 'required|string',
        'type' => 'required|string',
        'discord_id' => 'required|string',
        'note' => 'string'
    ];
}
