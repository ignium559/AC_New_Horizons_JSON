<?php
$json = file_get_contents("compiledFossils.json");

parseFossilJson($json);
function parseFossilJson($json) {
  $output = [];
  $arr = json_decode($json);

  foreach ($arr as $fossil) {
    if (isset($fossil->part_name) && isset($fossil->price)) {
      $output[] = parseItem($fossil);
    }
  }

  $output = json_encode($output);

  file_put_contents("fossils.json", $output);
}


class Fossil
{
  public $fossil;
  public $part_name;
  public $price;

  public function __construct($fossil, $part_name, $price)
  {
    $this->fossil = $fossil;
    $this->part_name = $part_name;
    $this->price = $price;
  }
}

function parseItem($arr) {
  if (! isset($arr->price)) return;

  $price = formatPrice($arr->price);

  $fossil = isset($arr->fossil) ? $arr->fossil : getParentFossil($arr->part_name);
  $part_name = $arr->part_name;

  return new Fossil($fossil, $part_name, $price);
}

function formatPrice($price)
{
  $price = str_replace('Bells', '', $price);
  $price = str_replace(',', '', $price);
  
  return (int)$price;
}


function getParentFossil($part_name) {
  $map = [
    'ankylo' => "Ankylosaurus",
    'archelon' => "Archelon",
    'brachio' => "Brachiosaurus",
    'deinony' => "Deinonychus",
    'dimetrodon' => "Dimetrodon",
    'diplo' => "Diplodocus",
    'iguanodon' => "Iguanodon",
    'mammoth' => "Mammoth",
    'megacero' => "Megacerops",
    'megalo' => "Megaloceros",
    'ophthalmo' => "Ophthalmosaurus",
    'pachysaurus' => "Pachycephalosaurus",
    'parasaur' => "Parasaurolophus",
    'plesio' => "Plesiosaurus",
    'ptera' => "Pteranodon",
    'quetzal' => "Quetzalcoatlus",
    'sabertooth' => "Sabertooth Tiger",
    'spino' => "Spinosaurus",
    'stego' => "Stegosaurus",
    'tricera' => "Triceratops",
    'rex' => "Tyrannosaurus Rex"
  ];

  foreach (array_keys($map) as $key) {
    if (strpos(strtolower($part_name), $key) !== false)
      return $map[$key];
  }

  return "************************************";
}