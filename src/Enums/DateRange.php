<?php

namespace JeffersonGoncalves\MetricsPlausible\Enums;

enum DateRange: string
{
    case Day = 'day';
    case Last7Days = '7d';
    case Last28Days = '28d';
    case Last30Days = '30d';
    case Last91Days = '91d';
    case Month = 'month';
    case Last6Months = '6mo';
    case Last12Months = '12mo';
    case Year = 'year';
    case All = 'all';
}
