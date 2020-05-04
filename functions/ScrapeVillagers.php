<?php
/**
 * Parse an HTML file for relevant villager data
 *
 * @param string $file
 * @param string $name
 * @return VillagerInfo
 */
function parseVillagerData($file, $name)
{
  $dom = new DOMDocument();
  $dom->loadHTML($file);

  $name = $dom->getElementById('firstHeading')->nodeValue;
  $species = $dom->getElementById('Infobox-villager-species')->nodeValue;
  $personality = $dom->getElementById('Infobox-villager-personality')->nodeValue;
  $gender = $dom->getElementById('Infobox-villager-gender')->nodeValue;
  
  $birthday = $dom->getElementById('Infobox-villager-birthday')->nodeValue;
  $birthday = new Birthday($birthday);

  $zodiac = $dom->getElementById('Infobox-villager-starsign');
  $zodiac = isset($zodiac) ? parseZodiac($zodiac) : '*****MISSING*****';

  $data_url = generateDataUrl($name);
  
  return new VillagerInfo($name, $species, $personality, $gender, $birthday, $zodiac, $data_url);
}

/**
 * Parse nested zodiac sign from image alt text
 *
 * @param DOMElement $node
 * @return string
 */
function parseZodiac($node)
{
  $img = $node->getElementsByTagName('img');
  return $img->item(0)->attributes->getNamedItem('alt')->value;
}

/**
 * Generate the data url for the villager based on name
 *
 * @param string $name
 * @return string
 */
function generateDataUrl($name)
{
  $files = [
    'Renée' => "https://nookipedia.com/wiki/Ren%C3%A9e",
    'June' => "https://nookipedia.com/wiki/June_(villager)",
    'Flora' => "https://nookipedia.com/wiki/Flora_(villager)",
    'Carmen' => "https://nookipedia.com/wiki/Carmen_(rabbit)",
    'Étoile' => "https://nookipedia.com/wiki/%C3%89toile",
    'Snooty' => "https://nookipedia.com/wiki/Snooty_(villager)",
  ];

  if (in_array($name, array_keys($files))) return $files[$name];

  $base = "https://nookipedia.com/wiki/";
  
  $name = str_replace(' ', '_', $name);

  return $base.$name;
}

/**
 * Villager Data Class
 */
class VillagerInfo
{
  public $name;
  public $species;
  public $personality;
  public $gender;
  public $birthday;
  public $zodiac;
  public $data_url;
  //public $coffee_order;  //Maybe in the future if The Roost gets included in the game

  /**
   * Constructor
   *
   * @param string $name
   * @param string $species
   * @param string $personality
   * @param string $gender
   * @param Birthday $birthday
   * @param string $zodiac
   * @param string $data_url
   */
  public function __construct($name, $species, $personality, $gender, Birthday $birthday, $zodiac, $data_url)//, CoffeeOrder $coffee_order = null)
  {
    $this->name = $this->dataExists($name);
    $this->species = $this->dataExists($species);
    $this->personality = $this->dataExists($personality);
    $this->gender = $this->dataExists($gender);
    $this->birthday = $birthday;
    $this->zodiac = $this->dataExists($zodiac);
    $this->data_url = $data_url;
    //$this->coffeeOrder = $coffee_order;
  }

  /**
   * Validate if a value exists and trim if it does
   *
   * @param string $value
   * @return string
   */
  function dataExists($value)
  {
    $value = trim($value);
    if ($value) {
      return $value;
    }

    return "****MISSING****";
  }
}

/**
 * Birthday Data Class
 */
class Birthday
{
  public $month;
  public $day;
  public $text;

  /**
   * Constructor
   *
   * @param string $text
   */
  public function __construct($text)
  {
    $text = trim($text);
    $this->text = $text !== '' ? $text : "****MISSING****";
    list($month, $day) = $this->parseBirthdayText($text);
    $this->month = $month;
    $this->day = $day;
  }

  /**
   * Parse the month and day from a birthday string
   *
   * @param string $text
   * @return array
   */
  public function parseBirthdayText($text)
  {
    if (trim($text) === '') return [0, 0];

    $months = ["January", "February", "March", "April",
               "May", "June", "July", "August", 
               "September", "October", "November", "December"];
    
    preg_match('/(\w+)\s(\d+)(st|nd|rd|th)?$/', trim($text), $matches);

    $month = array_search($matches[1], $months);
    $month = $month !== false ? $month + 1 : 0;
    $day = (int)$matches[2] ?? 0;

    return [$month, $day];
  }
}

/**
 * Coffee Order Data class
 * @todo Implement if coffee orders become included
 */
class CoffeeOrder
{
  public $type;
  public $milk;
  public $sugar;

  /**
   * Constructor
   *
   * @param string $type
   * @param string $milk
   * @param string $sugar
   */
  public function __construct($type, $milk, $sugar) {
    $this->type = $type;
    $this->milk = $milk;
    $this->sugar = $sugar;
  }
}

/**
 * CLI function to execute full script
 *
 * @param string $names_file
 * @param string $destination
 * @param string $scrape_destination_dir
 * @return void
 */
function run($names_file = null, $destination = null, $scrape_destination_dir = null)
{
  error_reporting(E_ALL ^ E_WARNING);

  if (! file_exists($names_file)) {
    echo "Error: Name file at location $names_file does not exist\n";
    echo "Exiting...";
    die();
  }

  $names = json_decode(file_get_contents($names_file));

  if (! file_exists($destination)) {
    $cwd = getcwd();
    echo "Warning: Destination file does not exist, using current directory ($cwd)\n";
    $destination = $cwd.'/output.json';
  }

  if ($scrape_destination_dir !== null && ! is_dir($scrape_destination_dir)) {
    echo "Scapped file destination directory doesn't exist use current folder? (Y/N)\n";
    $input = trim(fgets(STDIN));

    if (strtolower($input) !== 'y') {
      echo 'Exiting...';
      die();
    }

    $scrape_destination_dir = getcwd();
  }

  $output = [];

  foreach ($names as $name) {
    echo str_pad("Getting data for $name", 50, '=').PHP_EOL;
    $scrape_dest_set = $scrape_destination_dir !== null;

    $output_file = "$scrape_destination_dir/$name";
    $data_exists = file_exists($output_file);

    $source = $data_exists ? $output_file : generateDataUrl($name);

    $message = $data_exists ? "Data exists using file $source" : "Data doesn't exist, downloading from $source";
    echo $message.PHP_EOL;

    $data = file_get_contents($source);
 
    if ($scrape_dest_set && ! $data_exists) {
      echo "Writing data for $name to $output_file\n";
      file_put_contents($output_file, $data);
    }

    $output[] = parseVillagerData($data, $name);
    echo str_pad("Data for $name added to array", 50, '-').PHP_EOL;
  }

  $output = json_encode($output, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
  file_put_contents($destination, $output);
  echo "Output complete, final JSON file located at $destination";
}

$names_file = $argv[1] ??= null;
$destination = $argv[2] ??= null;
$scrape_destination_dir = $argv[3] ??= null;

run($names_file, $destination, $scrape_destination_dir);