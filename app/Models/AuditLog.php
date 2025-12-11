<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'action',
        'module',
        'model_type',
        'model_id',
        'description',
        'reason',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log an action to the audit log
     */
    public static function log(
        string $action,
        string $module,
        string $description,
        ?Model $model = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $reason = null
    ): self {
        $user = Auth::user();
        
        return self::create([
            'user_id' => $user?->id,
            'user_name' => $user?->name ?? 'System',
            'action' => $action,
            'module' => $module,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->id,
            'description' => $description,
            'reason' => $reason,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log a model creation
     */
    public static function logCreated(string $module, Model $model, string $description, ?string $reason = null): self
    {
        return self::log('created', $module, $description, $model, null, $model->toArray(), $reason);
    }

    /**
     * Log a model update
     */
    public static function logUpdated(string $module, Model $model, array $oldValues, string $description, ?string $reason = null): self
    {
        $changedValues = [];
        foreach ($model->getDirty() as $key => $value) {
            $changedValues[$key] = $value;
        }
        
        return self::log('updated', $module, $description, $model, $oldValues, $changedValues, $reason);
    }

    /**
     * Log a model deletion
     */
    public static function logDeleted(string $module, Model $model, string $description, ?string $reason = null): self
    {
        return self::log('deleted', $module, $description, $model, $model->toArray(), null, $reason);
    }

    /**
     * Log an approval action
     */
    public static function logApproved(string $module, Model $model, string $description, ?string $reason = null): self
    {
        return self::log('approved', $module, $description, $model, null, null, $reason);
    }

    /**
     * Log a rejection action
     */
    public static function logRejected(string $module, Model $model, string $description, ?string $reason = null): self
    {
        return self::log('rejected', $module, $description, $model, null, null, $reason);
    }

    /**
     * Log a loan modification
     */
    public static function logLoanModified(Loan $loan, array $oldValues, array $newValues, string $description, ?string $reason = null): self
    {
        return self::log('loan_modified', 'loans', $description, $loan, $oldValues, $newValues, $reason);
    }

    /**
     * Get formatted action label
     */
    public function getActionLabelAttribute(): string
    {
        return match($this->action) {
            'created' => 'Created',
            'updated' => 'Updated',
            'deleted' => 'Deleted',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'loan_modified' => 'Loan Modified',
            'emi_changed' => 'EMI Changed',
            'tenure_changed' => 'Tenure Changed',
            'pre_closure' => 'Pre-Closure',
            'disbursed' => 'Disbursed',
            'payment_collected' => 'Payment Collected',
            default => ucfirst(str_replace('_', ' ', $this->action)),
        };
    }

    /**
     * Get formatted module label
     */
    public function getModuleLabelAttribute(): string
    {
        return match($this->module) {
            'employees' => 'Employees',
            'loans' => 'Loans',
            'requests' => 'Requests',
            'teams' => 'Teams',
            'payments' => 'Payments',
            'salary' => 'Salary',
            'users' => 'Users',
            default => ucfirst(str_replace('_', ' ', $this->module)),
        };
    }

    /**
     * Get action color class for UI
     */
    public function getActionColorAttribute(): string
    {
        return match($this->action) {
            'created' => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
            'updated', 'loan_modified', 'emi_changed', 'tenure_changed' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
            'deleted' => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
            'approved', 'disbursed', 'payment_collected' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/20 dark:text-amber-400',
            'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
            'pre_closure' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        };
    }
}
