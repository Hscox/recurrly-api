<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    /**
     * Default pastel accent colors mapped by subscription category.
     *
     * @var array<string, string>
     */
    public const array CATEGORY_COLORS = [
        'AI Tools' => '#cfe2fe',
        'Entertainment' => '#ff7666',
        'Developer Tools' => '#ded2f9',
        'Design' => '#eed766',
        'Productivity' => '#eadece',
        'Other' => '#fed7aa',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'price',
        'frequency',
        'category',
        'billing',
        'firstBill',
        'paymentMethod',
        'color',
    ];

    /**
     * Get the ordered list of supported subscription categories.
     *
     * @return list<string>
     */
    public static function categories(): array
    {
        return array_keys(self::CATEGORY_COLORS);
    }

    /**
     * Resolve the pastel accent color for a given category name.
     */
    public static function colorForCategory(string $category): string
    {
        return self::CATEGORY_COLORS[$category] ?? '#fed7aa';
    }

    /**
     * Parse the stored price string into a numeric float value.
     */
    public function numericPrice(): float
    {
        $cleaned = preg_replace('/[^0-9.]/', '', (string) $this->price);

        return (float) ($cleaned ?? 0);
    }

    /**
     * Format the subscription's stored price in Malaysian Ringgit (MYR).
     */
    public function formattedPrice(): string
    {
        return 'RM '.number_format($this->numericPrice(), 2);
    }

    /**
     * Determine the normalized billing cycle label for this subscription.
     */
    public function billingCycle(): string
    {
        if (filled($this->frequency)) {
            return (string) $this->frequency;
        }

        if (filled($this->billing)) {
            return (string) $this->billing;
        }

        return 'Monthly';
    }

    /**
     * Calculate the monthly equivalent cost of this subscription.
     */
    public function monthlyEquivalent(): float
    {
        $amount = $this->numericPrice();

        if (strtolower($this->billingCycle()) === 'yearly') {
            return $amount / 12;
        }

        return $amount;
    }

    /**
     * Calculate the annualized cost of this subscription.
     */
    public function yearlyEquivalent(): float
    {
        $amount = $this->numericPrice();

        if (strtolower($this->billingCycle()) === 'yearly') {
            return $amount;
        }

        return $amount * 12;
    }

    /**
     * Generate a two-character uppercase monogram from the subscription name.
     */
    public function monogram(): string
    {
        $trimmed = trim((string) $this->name);

        if ($trimmed === '') {
            return 'SU';
        }

        return strtoupper(substr($trimmed, 0, 2));
    }
}
