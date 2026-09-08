<?php

namespace JeffersonGoncalves\MetricsPlausible\Enums;

enum Dimension: string
{
    case Page = 'event:page';
    case Hostname = 'event:hostname';
    case EventName = 'event:name';
    case Goal = 'event:goal';

    case Source = 'visit:source';
    case Referrer = 'visit:referrer';
    case Channel = 'visit:channel';
    case UtmSource = 'visit:utm_source';
    case UtmMedium = 'visit:utm_medium';
    case UtmCampaign = 'visit:utm_campaign';
    case UtmContent = 'visit:utm_content';
    case UtmTerm = 'visit:utm_term';

    case Device = 'visit:device';
    case Browser = 'visit:browser';
    case BrowserVersion = 'visit:browser_version';
    case Os = 'visit:os';
    case OsVersion = 'visit:os_version';

    case Country = 'visit:country';
    case CountryName = 'visit:country_name';
    case Region = 'visit:region';
    case RegionName = 'visit:region_name';
    case City = 'visit:city';
    case CityName = 'visit:city_name';

    case EntryPage = 'visit:entry_page';
    case ExitPage = 'visit:exit_page';

    case TimeMinute = 'time:minute';
    case TimeHour = 'time:hour';
    case TimeDay = 'time:day';
    case TimeWeek = 'time:week';
    case TimeMonth = 'time:month';

    /**
     * Custom property dimension, e.g. `event:props:plan`.
     */
    public static function property(string $key): string
    {
        return 'event:props:'.$key;
    }
}
