<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'status',
        'day_of_week',
        'user_id' // Added if you want user association
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Days of the week constants
     */
    public const DAYS_OF_WEEK = [
        'Senin' => 'Monday',
        'Selasa' => 'Tuesday',
        'Rabu' => 'Wednesday',
        'Kamis' => 'Thursday',
        'Jumat' => 'Friday',
        'Sabtu' => 'Saturday'
    ];

    /**
     * Task status constants
     */
    public const STATUSES = [
        'belum_mulai' => 'Not Started',
        'proses' => 'In Progress',
        'selesai' => 'Completed'
    ];

    /**
     * Get the user that owns the task.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for filtering by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by day of week
     */
    public function scopeDayOfWeek($query, $day)
    {
        return $query->where('day_of_week', $day);
    }

    /**
     * Get the translated day name
     */
    public function getTranslatedDayAttribute()
    {
        return self::DAYS_OF_WEEK[$this->day_of_week] ?? $this->day_of_week;
    }

    /**
     * Get the translated status name
     */
    public function getTranslatedStatusAttribute()
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /**
     * Check if task is completed
     */
    public function isCompleted()
    {
        return $this->status === 'selesai';
    }

    /**
     * Check if task is in progress
     */
    public function isInProgress()
    {
        return $this->status === 'proses';
    }

    /**
     * Check if task is not started
     */
    public function isNotStarted()
    {
        return $this->status === 'belum_mulai';
    }
}