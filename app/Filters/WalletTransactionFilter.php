<?php

namespace App\Filters;

use App\Filters\ApiFilter;

class WalletTransactionFilter extends ApiFilter {
    /**
     * Map query parameters to database columns.
     */
    protected $safeParms = [
        'type' => ['eq'],
        'status' => ['eq'],        
        'amount' => ['eq', 'gt', 'lt', 'gte', 'lte'],        
        'reference' => ['eq'],        
        'created_at' => ['eq', 'gt', 'lt']
    ];

    /**
     * Map shorthand operators to SQL operators.
     */
    protected $operatorMap = [
        'eq' => '=',
        'lt' => '<',
        'lte' => '<=',
        'gt' => '>',
        'gte' => '>='
    ];
}