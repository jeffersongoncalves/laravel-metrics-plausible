<?php

namespace JeffersonGoncalves\MetricsPlausible\Enums;

enum Metric: string
{
    case Visitors = 'visitors';
    case Visits = 'visits';
    case Pageviews = 'pageviews';
    case ViewsPerVisit = 'views_per_visit';
    case BounceRate = 'bounce_rate';
    case VisitDuration = 'visit_duration';
    case Events = 'events';
    case ScrollDepth = 'scroll_depth';
    case Percentage = 'percentage';
    case ConversionRate = 'conversion_rate';
    case GroupConversionRate = 'group_conversion_rate';
    case TimeOnPage = 'time_on_page';
    case ExitRate = 'exit_rate';
    case AverageRevenue = 'average_revenue';
    case TotalRevenue = 'total_revenue';
}
