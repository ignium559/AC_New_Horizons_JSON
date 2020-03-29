<?php

$json = file_get_contents("Fish.json");

$arr = json_decode($json);

$output = [];

/*
// $test = $arr[0]; //Pale Chub (Year round)
// $test = $arr[24]; //Sweetfish (Basic months)
// $test = $arr[25];  //Cherry Salmon (Complex months)
// $test = $arr[12]; //Tadpole (year loop months)

var_dump($test->name);

$times = new Information(parseTimeToArray($test->times), $test->times);
$months = processMonths($test->months);

$obj = new FishData($test, $times, $months);

var_dump($obj);
*/

foreach ($arr as $fish) {
    $output[] = processJSONItem($fish);
}

$data = json_encode($output);

file_put_contents("output.json", $data);

echo '***************** COMPLETE ********************';

class Information
{
    public $array;
    public $text;

    public function __construct(array $array, string $text)
    {
        $this->array = $array;
        $this->text = $text;
    }
}

class MonthData
{
    public $northern;
    public $southern;

    public function __construct(Information $northern, Information $southern)
    {
        $this->northern = $northern;
        $this->southern = $southern;
    }
}

class FishData
{
    public $id;
    public $name;
    public $location;
    public $shadow_size;
    public $price;
    public $times;
    public $months;

    public function __construct(Object $data, Information $times, MonthData $months)
    {

        $this->id = (int) $data->id;
        $this->name = $data->name;
        $this->location = $data->location;
        $this->shadow_size = $data->shadow_size;
        $this->price = (int) $data->price;
        $this->times = $times;
        $this->months = $months;
    }
}

function processJSONItem($item)
{
    $times = new Information(parseTimeToArray($item->times), $item->times);
    $months = processMonths($item->months);

    return new FishData($item, $times, $months);
}

function parseTimeToArray($string)
{
    if (strtolower($string) === "all day") {
        return createAllDayArray();
    }

    $output = [];
    $times = explode(',', $string);

    foreach ($times as $range) {
        [$start, $end] = explode(' - ', $range);

        $output[] = processTimeRange($start, $end);
    }

    $array = flatten($output);

    return $array;
}

function processTimeRange($start, $end)
{
    $start = convertTimesToDateTime($start);
    $end = convertTimesToDateTime($end);

    return createTimeArray($start, $end);
}

function convertTimesToDateTime($string)
{
    $string = trim(str_replace('.', '', $string));

    return DateTime::createFromFormat('g a', $string);
}

function isLoopedRange($start, $end)
{
    return $start > $end;
}

function createTimeArray($start, $end)
{
    if (isLoopedRange($start, $end)) {
        return createOvernightArray($start, $end);
    }

    return createBasicTimeArray($start, $end);
}

function createBasicTimeArray($start, $end)
{
    return range($start->format('G'), ($end->format('G') - 1));
}

function createOvernightArray($start, $end)
{
    $end_range = range($start->format('G'), 23);
    $beginning_range = range(0, $end->format('G') - 1);

    return array_merge($beginning_range, $end_range);
}

function createAllDayArray()
{
    return range(0, 23);
}

function processMonths($string)
{
    if (strtolower($string) === "year-round (northern and southern)") {
        return createYearRoundMonthData();
    }

    [$north, $south] = explode(' / ', $string);

    $north = processHemisphere($north);
    $south = processHemisphere($south);

    return new MonthData($north, $south);
}

function processHemisphere($hemisphere)
{
    $hemisphere = stripHemisphereString($hemisphere);

    $months = explode(', ', $hemisphere);

    $ranges = [];

    foreach ($months as $range) {
        [$start, $end] = explode('-', $range);

        $ranges[] = processMonthRange($start, $end);
    }

    $array = flatten($ranges);
    $text = str_replace('-', ' - ', $hemisphere);

    return new Information($array, $text);
}

function flatten($multidim_array)
{
    $output = [];

    foreach ($multidim_array as $arr) {
        foreach ($arr as $item) {
            $output[] = $item;
        }
    }

    sort($output);

    return $output;
}

function stripHemisphereString($string)
{
    $pattern = '/\((Nor|Sou)thern\)/';
    return trim(preg_replace($pattern, '', $string));
}

function processMonthRange($start, $end = null)
{
    $start = convertMonthToDateTime($start);
    $end = isset($end) ? convertMonthToDateTime($end) : null;

    if (!isset($end)) {
        return [(int) $start->format('n')];
    }

    if (isLoopedRange($start, $end)) {
        return createNewYearMonthArray($start, $end);
    }

    return createBasicMonthArray($start, $end);
}

function convertMonthToDateTime($string)
{
    return DateTime::createFromFormat('F j H:i:s', $string . '1 00:00:00');
}

function createNewYearMonthArray($start, $end)
{
    $end_range = range($start->format('n'), 12);
    $beginning_range = range(1, $end->format('G'));

    return array_merge($beginning_range, $end_range);
}

function createBasicMonthArray($start, $end)
{
    return range($start->format('n'), $end->format('n'));
}

function createYearRoundMonthData()
{
    $array = range(1, 12);
    $text = "Year Round";

    $northern = new Information($array, $text);
    $southern = new Information($array, $text);

    return new MonthData($northern, $southern);
}
