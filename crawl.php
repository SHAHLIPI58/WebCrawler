<?php

set_time_limit(10000000000000000000);
for ($z=1;$z<141;$z++)
{


    $file = file_get_contents("http://www.food.com/recipe?categories=265&pn=$z","r");
    
    $start = stripos($file,"var searchResults = ");

    $end = stripos($file,'"pageview_candidate"};');
    $length = ($end-$start)+1;
    // echo $length;
    $jsondata = substr($file,$start+20,$length);
    

    $json_array = json_decode($jsondata, true);
    $numRecords=$json_array['response']['parameters']['numRecords'];
for ($y=0;$y<$numRecords;$y++)
{
	$servername = "localhost";
$username = "root";
$password = "";
$dbname = "recipedb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 



//Gives needed data
$main_num_ratings=$json_array['response']['results'][$y]['main_num_ratings'];
if($main_num_ratings>3)
{
$url=$json_array['response']['results'][$y]['record_url'];
$recipe_id=$json_array['response']['results'][$y]['recipe_id']; 
$main_title=$json_array['response']['results'][$y]['main_title']; 
$main_title=str_replace("'"," ",$main_title);
$main_title=str_replace("/"," ",$main_title);
$num_steps=$json_array['response']['results'][$y]['num_steps'];
$main_submit_date=$json_array['response']['results'][$y]['main_submit_date'];
$submitdate=date("Y-m-d",$main_submit_date);
$recipe_preptime=$json_array['response']['results'][$y]['recipe_preptime']; 
$recipe_totaltime=$json_array['response']['results'][$y]['recipe_totaltime']; 
$recipe_cooktime=$json_array['response']['results'][$y]['recipe_cooktime']; 
$time='recipe_preptime='.$recipe_preptime.','.'recipe_totaltime='.$recipe_totaltime.','.'recipe_cooktime='.$recipe_cooktime;
$main_rating=$json_array['response']['results'][$y]['main_rating'];


   
$file1 = file_get_contents($url);
//print_r($file1);

//$keywords
$start2 = stripos($file1,'"keywords":');
$end2 = stripos($file1,'","SctnDspName');
$length2 = ($end2-$start2)-12;
$keywords = substr($file1,$start2+12,$length2);



//$keywordids
$start2 = stripos($file1,'"keywordids":');
$end2 = stripos($file1,'","keywords');
$length2 = ($end2-$start2)-14;
$keywordids = substr($file1,$start2+14,$length2);
//print_r($keywordids);

//ingridients
$start2 = stripos($file1,',"ingredients":');
$end2 = stripos($file1,'","keywordids"');
$length2 = ($end2-$start2)-16;
$ingridients = substr($file1,$start2+16,$length2);
//print_r($ingridients);

//json_data

$file1 = file_get_contents($url);
$start = stripos($file1,'+json');
$end = stripos($file1,'}</script>');
// print_r($start);
// print_r($end);
$length = ($end-$start)+4;

    $jsondata1 = substr($file1,$start+7,$length);
    $jsondata1 = strip_tags($jsondata1);


    $json_array1 = json_decode($jsondata1, true);
   //nutrition contents
	$calories="calories=".$json_array1['nutrition']['calories'];
	$fatContent="fatcaloriesContent=".$json_array1['nutrition']['fatContent'];
	$saturatedFatContent="saturatedFatContent=".$json_array1['nutrition']['saturatedFatContent'];
	$cholesterolContent="cholesterolContent=".$json_array1['nutrition']['cholesterolContent'];
	$sodiumContent="sodiumContent=".$json_array1['nutrition']['sodiumContent'];
	$carbohydrateContent="carbohydrateContent=".$json_array1['nutrition']['carbohydrateContent'];
	$fiberContent="fiberContent=".$json_array1['nutrition']['fiberContent'];
	$sugarContent="sugarContent=".$json_array1['nutrition']['sugarContent'];
	$proteinContent="proteinContent=".$json_array1['nutrition']['proteinContent'];
	$nutrition=$calories." ".$fatContent." ".$saturatedFatContent." ".$cholesterolContent." ".$sodiumContent." ".$carbohydrateContent." ".$fiberContent." ".$sugarContent." ".$proteinContent;
	//print_r($nutrition);
	$recipeInstructions="";
	//recipeInstructions
	for ($x = 0; $x <= $num_steps-1; $x++)
	{
	$recipeInstructions=$recipeInstructions." ".$json_array1['recipeInstructions']['itemListElement'][$x];
	}
//print_r($recipeInstructions);
	$recipeInstructions=str_replace("'"," ",$recipeInstructions);
	$recipeInstructions=str_replace("/"," ",$recipeInstructions);


$fpjson = fopen('datajson.js', 'w');
	// fwrite($fp, '1');
	fwrite($fpjson, $jsondata);



    $fp = fopen('data.txt', 'w');
	// fwrite($fp, '1');
	fwrite($fp, $file);
    $fpjson1 = fopen('datajson1.js', 'w');
	// fwrite($fp, '1');
	fwrite($fpjson1, $jsondata1);


    $fp1 = fopen('data.txt', 'w');
	// fwrite($fp, '1');
	fwrite($fp1, $file1);
	$sql = "INSERT INTO recipes(recipeId,recipename,average_rating,ingredients,features,feature_id,time,recipeinstruction,nutrition,no_of_steps,c_date)VALUES($recipe_id,'$main_title',$main_rating,'$ingridients','$keywords','$keywordids','$time','".$recipeInstructions."','$nutrition',$num_steps,'$submitdate')";
	

if ($conn->query($sql) === TRUE) {
   
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();

}
}
	

	
}
?>
