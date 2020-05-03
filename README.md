<h2>AC: New Horizons JSON data</h2>
<p>Contains data for Animal Crossing: New Horizons in JSON format.</p>

<h5>Structure for fish:</h5>
<pre>
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
</pre>

<h5>Structure for insects</h5>
<pre>
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
</pre>

<h5>Structure for fossils</h5>
<pre>
fossil                   //Name of parent fossil (string)
part_name                //Individual part's name (the item assessed by Blathers) (string)
price                    //Price assessed fossil can be sold for (int)
</pre>

<p>This is the current data I have (scrapped from the internet), pull requests are welcome (but please stick to the schema).
