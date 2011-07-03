<?php
include("config.php");

function select_columns($var)
{
	global $column_list;
        if (!preg_match("/($column_list)/", $var['name']) ) { return ($var & 1); }
}
function column_wrapper($array, $filter) {  // Wrapper for select_columns
	if (! $filter ) { return $array; }
	global $column_list;
	$column_list = $filter;
	$filtered_array = array_filter($array, "select_columns");
	$column_list = "";
	return $filtered_array;
}
function serverDetails($serverhost, $serverport) {
// Pull Variables from Running Hopmod Server
	global $server_title;
	$server_title = GetHop("servername", $serverhost, $serverport);
	if ( ! isset($server_title) ) { $server_title = "SuckerServ Server";} //Set it to something
}

function count_rows($query) {
	global $dbh;
	$count = $dbh->query($query) or die(print_r($dbh->errorInfo()));
	return $count->fetchColumn();
	
}
function startbench() {
	global $starttime;
	$mtime = microtime();
	$mtime = explode(' ', $mtime);
	$mtime = $mtime[1] + $mtime[0];
	$starttime = $mtime;
	return $starttime;
}
function stopbench() {
	global $starttime;
	$mtime = microtime();
	$mtime = explode(" ", $mtime);
	$mtime = $mtime[1] + $mtime[0];
	$endtime = $mtime;
	$totaltime = ($endtime - $starttime);
?>
<div id="footer">
<span id="date">This page was last updated <?php print date("F j, Y, g:i a"); ?> .</span> | <a href="http://www.sauerbraten.org">Sauerbraten.org</a> | <a href="http://suckerserv.googlecode.com">SuckerServ</a>
<?php echo '<p>This page was created in ' .round($totaltime,5). ' seconds using 2 querys.</p>'; ?>
</div>
<?php
}
function GetHop($cubescript, $serverhost, $serverport) {

        $content_length = strlen($cubescript);
        $headers= "POST /serverexec HTTP/1.0\r\nContent-type: text/x-cubescript\r\nHost: ".$serverhost.":".$serverport."\r\nContent-length: $content_length\r\n\r\n";
        $fp = fsockopen($serverhost.":".$serverport);
        if (!$fp) return false;
        fputs($fp, $headers);
        fputs($fp, $cubescript);
        $ret = "";
        while (!feof($fp)) {
                $ret = fgets($fp, 1024);
        }
        fclose($fp);
        return $ret;
}
function overlib($overtext,$heading = "") {
        print "<a  href=\"javascript:void(0);\" onmouseover=\"return overlib('$overtext');\" onmouseout=\"return nd();\">$heading</a>" ;
}
function overlib2($overtext,$heading = "") {
        return "<a  href=\"javascript:void(0);\" onmouseover=\"return overlib('$overtext');\" onmouseout=\"return nd();\">$heading</a>"
;
}
function setup_pdo_statsdb($db) {
	try {
		if ($db['type'] == "mysql")
		{
			$db_string = "mysql:dbname=".$db['name'].";host=".$db['host'];
			$dbh = new PDO($db_string, $db['user'], $db['pass']);
		}
		elseif ($db['type'] == "sqlite3")
		{ 
			$db_string = "sqlite:".$db['path'];
	                $dbh = new PDO($db_string);
		}
	}
	catch(PDOException $e)
	{
	        echo $e->getMessage();
	}
	return $dbh;
}
function build_pager ($page, $query) {
	// current_page query link enable filtering display
	global $dbh;
	global $rows_per_page;
	$count = $dbh->query($query) or die(print_r($dbh->errorInfo()));
	$rows = $count->fetchColumn();
    foreach ( $count as $test) {echo $test;}
	$pages = ( ceil($rows / $rows_per_page) );
	print "<div style=\"float: right \" class=\"pagebar\">";
	if ( ! isset($page) ) { $page = 1; }
	if ( $page <= "1" or $page > $pages ) {
	        $page == "1";
	} else {
	        $nextpage = ($page - 1);
	        print "\n<a href=\"?page=$nextpage\" >&#171; Prev</a>\n";
	}
	
	for ( $counter = 1; $counter <= $pages; $counter++) {
            if ($counter == $page) { $class = " class=\"selected\""; } else { $class = ""; }
	        print "<a href=\"?page=$counter&orderby=${_SESSION['orderby']}\"$class>$counter</a>";
	}
	if ( $page >= $pages or $page < "1" ) {
	        $page == $pages;
	} else {
	        $nextpage = ($page + 1);
	        print "\n<a href=\"?page=$nextpage&orderby=${_SESSION['orderby']}\" >Next &#187;</a>\n";
	}
	print overlib("Filtering in affect<br />Filter MinimumGames <font color=white>".$_SESSION['MinimumGames']."</font>Filter NoFrags","$rows results");
	print "</div>";
}
function check_get ($pagename) {
	global $rows_per_page;
	switch ($_GET['querydate']) {
	        case "day":
	                $_SESSION['querydate'] = "day";
	                $_SESSION['MinimumGames'] = "1";
	        break;
	        case "week":
	                $_SESSION['querydate'] = "week";
	                $_SESSION['MinimumGames'] = "2";
	        break;
	        case "month":
	                $_SESSION['querydate'] = "month";
	                $_SESSION['MinimumGames']  = "2";
	        break;
	        case "year":
	                $_SESSION['querydate'] = "year";
	                $_SESSION['MinimumGames'] = "3";
	        break;
	default:
	        if ( ! isset($_SESSION['querydate']) ) { $_SESSION['querydate'] = "month"; }
		if ( ! isset($_SESSION['MinimumGames']) ) { $_SESSION['MinimumGames'] = 4; }
	}
	
	if ( $_GET['page'] >= 2 ) {
	        $_SESSION['paging'] = ( ($_GET['page'] * $rows_per_page) - $rows_per_page +1 );
	} else { $_SESSION['paging'] = 0; }
	
	if ( isset($_GET['orderby']) ) {
        // Input Validation
        if (($pagename == "scoreboard") or ($pagename == "Daily Activity") or ($pagename == "game details")) {
            if (preg_match("/(Kpd|Accuracy|TotalGames|name|country|TotalScored|MostFrags|TotalFrags|TotalDeaths|TotalTeamkills)/i", $_GET['orderby']) ) {
                $_SESSION['orderby'] = $_GET['orderby'];
            }
        } elseif ($pagename == "player details") {
            if (preg_match("/(id|servername|datetime|duration|mapname|gamemode|players)/i", $_GET['orderby']) ) {
                $_SESSION['orderby'] = $_GET['orderby'];
            }
        }
	} elseif ( isset($_SESSION['orderby']) ) {
        // Input Validation
        if (($pagename == "scoreboard") or ($pagename == "Daily Activity") or ($pagename == "game details")) {
            if (preg_match("/(Kpd|Accuracy|TotalGames|name|country|TotalScored|MostFrags|TotalFrags|TotalDeaths|TotalTeamkills)/i", $_SESSION['orderby']) ) {
                 $_SESSION['orderby'] = $_SESSION['orderby'];
            } else {
                $_SESSION['orderby'] = "TotalScored";
            }
        } elseif ($pagename == "player details") {
            if (preg_match("/(id|servername|datetime|duration|mapname|gamemode|players)/i", $_GET['orderby']) ) {
                $_SESSION['orderby'] = $_SESSION['orderby'];
            } else
                 $_SESSION['orderby'] = "datetime";
            }
	} else {
        if (($pagename == "scoreboard") or ($pagename == "Daily Activity") or ($pagename == "game details")) {
                    $_SESSION['orderby'] = "TotalScored";
        } elseif ($pagename == "player details") {
                    $_SESSION['orderby'] = "datetime";
        }
    }
	if ( isset($_GET['name']) ) { $_SESSION['name'] = $_GET['name']; }
}
function stats_table ($query = "NULL" ,$exclude_columns = "NULL"){
	global $dbh;
	global $column_list; 
    global $rows_per_page;

//Table options
$stats_table = array (
    array("name" => "Name", "description" => "Players Nick Name", "column" => "name"),
    array("name" => "Country", "description" => "Players Country", "column" => "ipaddr"),
    array("name" => "Total Score", "description" => "The total score for all games", "column" => "TotalScored"),
    array("name" => "Frags Record", "description" => "The most frags ever acheived in one game", "column" => "MostFrags"),
    array("name" => "Total Frags", "description" => "The total number of frags for all games", "column" => "TotalFrags"),
    array("name" => "Total Deaths", "description" => "The total number of deaths for all games", "column" => "TotalDeaths"),
    array("name" => "Accuracy", "description" => "The percentage of shots fired that resulted in a frag", "column" => "Accuracy"),
    array("name" => "KpD", "description" => "The number of frags made before being killed", "column" => "Kpd"),
    array("name" => "TK", "description" => "The number of times a team member was fragged", "column" => "TotalTeamkills"),
    array("name" => "Games", "description" => "The total number of games played", "column" => "TotalGames"),
);
	$sql = "
select *
from
        (select name,
                ipaddr,
                sum(score) as TotalScored,
                sum(teamkills) as TotalTeamkills,
                max(frags) as MostFrags,
                sum(frags) as TotalFrags,
                sum(deaths) as TotalDeaths,
                count(name) as TotalGames,
                round((0.0+sum(hits))/(sum(hits)+sum(misses))*100) as Accuracy,
                round((0.0+sum(frags))/sum(deaths),2) as Kpd
        from players
                inner join games on players.game_id=games.id

        where UNIX_TIMESTAMP(games.datetime) between ".$_SESSION['start_date']." and ".$_SESSION['end_date']." and frags > 0 group by name order by ". $_SESSION['orderby']." desc) T
where TotalGames >= ". $_SESSION['MinimumGames'] ." limit ".$_SESSION['paging'].",$rows_per_page ;

";
	if ( $query !="NULL") { $sql = $query; }
	$result = $dbh->query($sql) or die(print_r($dbh->errorInfo()));
	$gi = geoip_open("/usr/share/GeoIP/GeoIP.dat",GEOIP_STANDARD);
?>
<table cellpadding="0" cellspacing="0" id="hopstats" class="tablesorter">
        <thead>
        <tr>
<?php
	foreach (column_wrapper($stats_table, $exclude_columns) as $column) { print "<th>";overlib($column['description'], $column['name']); print "</th>"; }
	print "</tr></thead><tbody>";
    $pair = 0;
	foreach ($result as $row)
	{
                    $pair++;
                    if ($pair%2 == 1) { $parity = "unpair"; } else { $parity = "pair"; }
	                $country = geoip_country_name_by_addr($gi, $row['ipaddr']);
	                $code = geoip_country_code_by_addr($gi, $row['ipaddr']);
	                if (isset($code)) {
	                        $code = strtolower($code) . ".png";
	                        $flag_image = "<img src=\"images/flags/$code\" alt=\"$country\" />";
	                }
	                print "
	                        <tr class=\"$parity\" onmouseover=\"this.className='highlight'\" onmouseout=\"this.className='$parity'\">
					<td><a href=\"player.php?name=$row[name]\">$row[name]</a></td>
	                                ";
	                                ?>
	                                <td><?php overlib($country,$flag_image);?></td>
	                                <?php
					foreach (column_wrapper($stats_table, "Name|Country|$exclude_columns") as $column) {
						print "<td>".$row[$column['column']]."</td>";
					}
	                print "
	                        </tr>\n";
	        $flag_image ="";
	}
// Close db handle
print "</tbody></table>";
}

function match_table ($game) {
        global $dbh;
        $sql3 = "
select 
	servername,
	datetime,
	duration,
	mapname,
	gamemode,
	players
from games 
where id = '$game' 

";
$result = $dbh->query($sql3) or die(print_r($dbh->errorInfo()));


        $gi = geoip_open("/usr/share/GeoIP/GeoIP.dat",GEOIP_STANDARD);
	$row = $result->fetch(PDO::FETCH_OBJ)
// Close db handle
?>

<div align="left" id="content"><h1>Game details</h1>
<div>

<table cellpadding="0" cellspacing="1">
<img style="float:right; margin-right:25%; border:0.5em ridge blue" src='images/maps/<?php print $row->mapname; ?>.jpg' />
<tr>
        <td class="headcol">Server</td>
        <td><?php print $row->servername ?></td>
</tr>
<tr>
        <td style="width:100px;" class="headcol">Date/Time</td>
        <td><?php print $row->datetime ?></td>
</tr>
<tr>
        <td class="headcol">Duration</td>
        <td><?php print $row->duration ?></td>
</tr>
<tr>
        <td class="headcol">Map</td>
        <td><?php print $row->mapname ?></td>
</tr>
<tr>
        <td class="headcol">Mode</td>
        <td><?php print $row->gamemode ?></td></tr>

</div>
<tr>
        <td class="headcol">Players</td>
        <td><?php print $row->players ?></td></tr>

</div>
</table>
</div></div>
<h2>Players</h2>
<?php
}

function match_player_table ($result ,$exclude_columns = "NULL"){
	global $column_list; 

//Table options
$desc_match_table = array (
    array("name" => "Game ID", "description" => "Global game number", "column" => "id"),
    array("name" => "Server", "description" => "The name of the server who started the game", "column" => "servername"),
    array("name" => "Date/Time", "description" => "Date and time when the game started", "column" => "datetime"),
    array("name" => "Duration", "description" => "Duration of the game in minutes", "column" => "duration"),
    array("name" => "Map", "description" => "Name of the played map", "column" => "mapname"),
    array("name" => "Mode", "description" => "Mode of the game", "column" => "gamemode"),
    array("name" => "Players", "description" => "The number of players during the game", "column" => "players"),
);

?>
<table cellpadding="0" cellspacing="0" id="matchstats" class="tablesorter">
        <thead>
        <tr>
<?php
	foreach (column_wrapper($desc_match_table, $exclude_columns) as $column) { print "<th>";overlib($column['description'], $column['name']); print "</th>"; }
	print "</tr></thead><tbody>";
    $pair = 0;
	foreach ($result as $row)
	{
                    $pair++;
                    if ($pair%2 == 1) { $parity = "unpair"; } else { $parity = "pair"; }
	                print "
	                        <tr class=\"$parity\" onmouseover=\"this.className='highlight'\" onmouseout=\"this.className='$parity'\">
					<td><a href=\"match.php?id=$row[id]\">$row[id]</a></td>
	                                ";

					foreach (column_wrapper($desc_match_table, "Game ID") as $column) {
						print "<td>".$row[$column['column']]."</td>";
					}
	                print "
	                        </tr>";
	        $flag_image ="";
	}
// Close db handle
print "</tbody></table>";
}

// Start page benchmark
startbench();

// Pull Variables from Running Hopmod Server
serverDetails($serverhost, $serverport);

// Start session for session vars
session_start();

// Check for any http GET activity
check_get($pagename);

// Setup statsdb and assign it to an object.
$dbh = setup_pdo_statsdb($db);

// Print headers
print <<<EOH
<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet type="text/css" href="/css/style.css"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
    "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
    <head>
        <title>$server_title's $pagename</title>
        <script type="text/javascript" src="js/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>
        <script type="text/javascript" src="js/jquery-latest.js"></script>
        <script type="text/javascript" src="js/jquery.tablesorter.js"></script>
        <script type="text/javascript" src="js/jquery.uitablefilter.js"></script>
        <script type="text/javascript" src="js/hopstats.js"></script>
        <link rel="stylesheet" type="text/css" href="css/style.css" />
    </head>
    <body>
        <div id="header">
            <span style="float:left;margin-right:10em"><a href="./"><img src="images/hopmod.png" alt="HopMod" /></a></span>
            <ul id="sddm">
EOH;

if (($pagename == "scoreboard") or ($pagename == "Daily Activity") or ($pagename == "game details")) {
print <<<EOH
                <li>
                    <a href="#" onmouseover="mopen('m1')"  onmouseout="mclosetime()">Ordered by <span style="color:blue">${_SESSION['orderby']}</span></a>
                    <div id="m1" onmouseover="mcancelclosetime()" onmouseout="mclosetime()">
                    <a style="border:none" href="?orderby=name">Name</a>
                    <a href="?orderby=country">Country</a>
                    <a href="?orderby=TotalScored">Total Score</a>
                    <a href="?orderby=MostFrags">Frags Record</a>
                    <a href="?orderby=TotalFrags">Total Frags</a>
                    <a href="?orderby=TotalDeaths">Total Deaths</a>
                    <a href="?orderby=Accuracy">Accuracy</a>
                    <a href="?orderby=Kpd">Kpd</a>
                    <a href="?orderby=TotalTeamkills">Total Teamkills</a>
                    <a href="?orderby=TotalGames">Total Games</a>
                    </div>
                </li>
EOH;
} elseif ($pagename == "player details") {
print <<<EOH
                <li>
                    <a href="#" onmouseover="mopen('m1')"  onmouseout="mclosetime()">Ordered by <span style="color:white">${_SESSION['orderby']}</span></a>
                    <div id="m1" onmouseover="mcancelclosetime()" onmouseout="mclosetime()">
                    <a href="?orderby=id&name=${_SESSION['name']}&page=${_GET['page']}">Game ID</a>
                    <a href="?orderby=servername&name=${_SESSION['name']}&page=${_GET['page']}">Server</a>
                    <a href="?orderby=datetime&name=${_SESSION['name']}&page=${_GET['page']}">Date/Time</a>
                    <a href="?orderby=duration&name=${_SESSION['name']}&page=${_GET['page']}">Duration</a>
                    <a href="?orderby=mapname&name=${_SESSION['name']}&page=${_GET['page']}">Map</a>
                    <a href="?orderby=gamemode&name=${_SESSION['name']}&page=${_GET['page']}">Mode</a>
                    <a href="?orderby=players&name=${_SESSION['name']}&page=${_GET['page']}">Players</a>
                    </div>
                </li>
EOH;
}
print <<<EOH
            </ul>

            <noscript><div class="error">This page uses JavaScript for table column sorting and producing an enhanced tooltip display.</div></noscript>
            <div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000"></div>
        </div>
EOH;

?>