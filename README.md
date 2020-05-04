## AC: New Horizons JSON data
Contains data for Animal Crossing: New Horizons in JSON format.

##### Structure for fish:
```
id                      //ID number of fish (int)
name                    //Name of fish (string)
location                //Location where fish can be found (string)
shadow_size             //Size of fish's shadow (string)
price                   //Price fish can be sold for (int)
times                   //Times fish is available
|_array                     //array format of times (e.g. [4, 5, 6, 7, 8])
|_text                      //text format of times (e.g. "4 a.m. - 9 a.m.")
months                  //Months fish is available
|_northern                  //...for northern hemisphere
|    |_array                    //array form of months (e.g. [1, 2, 3, 4])
|    |_text                     //text format of months (e.g. "January - April")
|_southern                  //...for southern hemisphere
     |_array                    //array form of months (e.g. [1, 2, 3, 4])
     |_text                     //text format of months (e.g. "January - April")
```

##### Structure for insects
```
id                      //ID number of insect (int)
name                    //Name of insect (string)
location                //Location where insect can be found (string)
price                   //Price insect can be sold for (int)
times                   //Times insect is available
|_array                     //array format of times (e.g. [4, 5, 6, 7, 8])
|_text                      //text format of times (e.g. "4 a.m. - 9 a.m.")
months                  //Months insect is available
|_northern                  //...for northern hemisphere
|    |_array                    //array form of months (e.g. [1, 2, 3, 4])
|    |_text                     //text format of months (e.g. "January - April")
|_southern                  //...for southern hemisphere
     |_array                    //array form of months (e.g. [1, 2, 3, 4])
     |_text                     //text format of months (e.g. "January - April")
```

##### Structure for fossils
```
fossil                   //Name of parent fossil (string)
part_name                //Individual part's name (the item assessed by Blathers) (string)
price                    //Price assessed fossil can be sold for (int)
```

##### Structure for Villagers
```
name                     //Villager's name (string)
species                  //Villager's species (string)
personality              //Villager's personality type (string)
gender                   //Villager's listed gender (string)
birthday                 //Object representing birthday (object)
|_month                  //integer value for birth month (e.g. 1 = January, 2 = February) (int)
|_day                    //integer value for day of birth (e.g. 1 = first, 2 = second) (int)
|_text                   //Text form of birthday (string)
zodiac                   //Villager's zodiac sign (string)
data_url                 //Page data was pulled from based on Creative Commons licence (string)          
```

The following is a list of villagers whose data appears in the game (and the json file), but remain unused at this time:
- Chai
- Chelsea
- Étoile
- Marty
- Rilla
- Toby

This is the current data I have (scrapped from the internet), pull requests are welcome (but please stick to the schema).
