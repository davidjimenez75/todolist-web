<?php
/**
 * Create a CSV with all the CSV LOGS from TODOLIST (http://www.abstractspoon.com)
 *
 * @todo - Some TimeSpent are with commas instead of decimal dots (spanish vs english)
 * @wip - Compatibility with new _log CSV files with UTF-8 instead of UTF-16
 * 
 */

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php';
use \Yalesov\FileSystemManager\FileSystemManager;
$todolist = new TodoList;

class TodoList
{
    public $version = "2023.11.05.1205";
    public $a_csv = array();
    public $a_tdl = array();
    public $a_html = array();
    public $debug = 0;
    public $logsDir = ".";
    public $rootDir = "c:\\xampp\\htdocs\\";

    
    /**
     * TodoListconstructor
     */
    public function __construct()
    {
        $this->csvList();
        $this->tdlList();
        $this->htmlList();

        // Unicode BOM is U+FEFF, but after encoded, it will look like this.
        define ('UTF32_BIG_ENDIAN_BOM'   , chr(0x00) . chr(0x00) . chr(0xFE) . chr(0xFF));
        define ('UTF32_LITTLE_ENDIAN_BOM', chr(0xFF) . chr(0xFE) . chr(0x00) . chr(0x00));
        define ('UTF16_BIG_ENDIAN_BOM'   , chr(0xFE) . chr(0xFF));
        define ('UTF16_LITTLE_ENDIAN_BOM', chr(0xFF) . chr(0xFE));
        define ('UTF8_BOM'               , chr(0xEF) . chr(0xBB) . chr(0xBF));


        // DEBUG
        if ($this->debug) {
            $this->csvListDebug();
            die();
        }
        
        $modes=array("screen", "screen_year", "screen_year_month", "download", "download_year", "download_year_month", "dokuwiki", "listcsv", "listtdl", "listhtml");

        if (isset($_GET["mode"]))
        {
            if (in_array($_GET["mode"],$modes))
            {
                $mode=$_GET["mode"];
                $this->$mode();
            }else{
                $this->help();   // show help
            }

        }else{
            $this->help();   // show help
        }
    }


        
    /**
     * Unicode BOM is U+FEFF, but after encoded, it will look like this.
     */
    public function detect_utf_encoding($filename) {
        $text = file_get_contents($filename);
        $first2 = substr($text, 0, 2);
        $first3 = substr($text, 0, 3);
        $first4 = substr($text, 0, 3);
        
        if ($first3 == UTF8_BOM) return 'UTF-8';
        elseif ($first4 == UTF32_BIG_ENDIAN_BOM) return 'UTF-32BE';
        elseif ($first4 == UTF32_LITTLE_ENDIAN_BOM) return 'UTF-32LE';
        elseif ($first2 == UTF16_BIG_ENDIAN_BOM) return 'UTF-16BE';
        elseif ($first2 == UTF16_LITTLE_ENDIAN_BOM) return 'UTF-16LE';
        else return 'UNKNOWN';
    }



    /**
     * Get the list of .csv files in the folder to $a_csv global array.
     */
    function csvList()
    {
        $ignored_strings=array(".hidden","zztodolist"); // Ignored files is containt any of this strings

        foreach (FileSystemManager::fileIterator($this->logsDir) as $file) {
            if ( (substr($file, -4) == ".csv") && (substr_count($file,"_Log")) ){
                
                $count=0;
                foreach($ignored_strings as $key=>$val)
                {
                    //echo $file." BUSCANDO: $val ($count)<br>"; //debug
                    $count+=substr_count($file, $val);
                }
                
                if ($count==0)
                {
                    // echo $file."<br>"; //debug
                    $this->a_csv[] = $file;
                }

            }
        }
    }

    /**
     * MODE: listcsv -> List all .csv files with direct link to download
     */
    function listcsv()
    {
        echo '<html>
        <head>
           <style type="text/css"><!--
              html { 
                  font-size: 11px!important; 
                  font-family: courier new;
              }
              a {
                  font-size: 11px!important; 
                  font-family: courier new;
     
                  text-decoration:none;
                  color: blue;
              }
              a:hover {
                  color:red;
              }
                   --></style>
        </head>
        <body>';
        $count=0;
        echo '<table border="0" cellspacing="0" cellpadding="2">'."\r\n";
        foreach($this->a_csv as $key=>$val)
        {
            echo '<tr><td><a href="'.$val.'" style="text-decoration:none;">'.substr($val,2).'</a>'."</td></tr>";
        }
        echo '</table>';
        echo '</body>';
        echo '</html>';

    }

    /**
     * Get the list of .tdl files in the folder to $a_tdl global array.
     */
    function tdlList()
    {
        $ignored_strings=array(".hidden","zztodolist"); // Ignored files is containt any of this strings

        foreach (FileSystemManager::fileIterator($this->logsDir) as $file) {
            if (substr($file, -4) == ".tdl") {
                
                $count=0;
                foreach($ignored_strings as $key=>$val)
                {
                    //echo $file." BUSCANDO: $val ($count)<br>"; //debug
                    $count+=substr_count($file, $val);
                }
                
                if ($count==0)
                {
                    // echo $file."<br>"; //debug
                    $this->a_tdl[] = $file;
                }

            }
        }
    }


    /**
     * MODE: listtdl -> List all .tdl files with direct link with tdl:// ()
     */
    function listtdl()
    {
        echo '<html>
        <head>
           <style type="text/css"><!--
              html { 
                  font-size: 11px!important; 
                  font-family: courier new;
              }
              a {
                  font-size: 11px!important; 
                  font-family: courier new;
     
                  text-decoration:none;
                  color:blue;
              }
              a:hover {
                  color:red;
              }
                   --></style>
        </head>
        <body>';
        $count=0;
        echo '<table border="0" cellspacing="0" cellpadding="2">'."\r\n";        
        foreach($this->a_tdl as $key=>$val)
        {
            echo '<tr><td><a href="tdl://'.$this->rootDir.substr($val,2).'" style="text-decoration:none;">'.substr($val,2).'</a>'."</td></tr>";
        }
        echo '</table>';
        echo '</body>';
        echo '</html>';
    }


    /**
     * Get the list of .csv files in the folder to $a_csv global array.
     */
    function htmlList()
    {
        $ignored_strings=array(".hidden","zztodolist"); // Ignored files is containt any of this strings

        foreach (FileSystemManager::fileIterator($this->logsDir) as $file) {
            if ( (substr($file, -5) == ".html") && (!substr_count($file,"vendor")) ){
                
                $count=0;
                foreach($ignored_strings as $key=>$val)
                {
                    //echo $file." BUSCANDO: $val ($count)<br>"; //debug
                    $count+=substr_count($file, $val);
                }
                
                if ($count==0)
                {
                    // echo $file."<br>"; //debug
                    $this->a_html[] = $file;
                }

            }
        }
    }


    /**
     * MODE: listtdl -> List all .tdl files with direct link with tdl:// ()
     */
    function listhtml()
    {
        echo '<html>
        <head>
           <style type="text/css"><!--
              html { 
                  font-size: 11px!important; 
                  font-family: courier new;
              }
              a {
                  font-size: 11px!important; 
                  font-family: courier new;
     
                  text-decoration:none;
                  color:blue;
              }
              a:hover {
                  color:red;
              }
                   --></style>
        </head>
        <body>';
        $count=0;
        echo '<table border="0" cellspacing="0" cellpadding="2">'."\r\n";        
        foreach($this->a_html as $key=>$val)
        {
            echo '<tr><td><a href="./'.$val.'" style="text-decoration:none;" target="_blank">'.substr($val,2).'</a>'."</td></tr>";
        }
        echo '</table>';
        echo '</body>';
        echo '</html>';
    }



    /**
     *
     */
    function csvListDebug()
    {
        // ARRAY CSV
        echo "<h2>CSV RECURSIVE LIST:</h2>";
        echo "<pre>";
        print_r($this->a_csv);
        echo "</pre>";

        // BOM CSV
        echo "<hr><h2>BOM CSV:</h2>";
        foreach ($this->a_csv as $key => $val)
        {
            $file_encoding="";
            echo $val;
            echo "=";
            echo $this->detect_utf_encoding($val);
            echo "<br>";
        }
    }



    /**
     * MODE: download -> Send a long csv with all the csv's 
     */
    function download()
    {
        $now = date('Y-m-d--His');
        $filename = 'zztodolists--' . $now . '--.csv';

        // CSV TITLE FIRST LINE
        $fp = fopen($filename, 'a');
        if ($this->detect_utf_encoding($val)=="UTF-16LE")
        {   
            $str = "Project\tTaskID\tTitle\tUserID\tStartDate\tStartTime\tEndDate\tEndTime\tTimeSpent\tComment\tType\tPath";
        }else{
            $str = "Project;TaskID;Title;UserID;StartDate;StartTime;EndDate;EndTime;TimeSpent;Comment;Type;Path";
        }
        fwrite($fp, $str);
        fclose($fp);


        foreach ($this->a_csv as $key => $val) {
            $temp = file_get_contents($val);


            $todotimelog = substr($temp, 2, 21);
            $todotimelog = str_replace(' ', '', $todotimelog);
            $todotimelog = serialize($todotimelog);

            $title = substr($val, strlen($this->logsDir) + 1, -8);

            // CSV IS CODED IN UCS-2 LE BOM
            if ($this->detect_utf_encoding($val)=="UTF-16LE")
            {                
                $temp = iconv('UCS-2LE', 'UTF-8', substr($temp, 0));//last byte was invalid
                $csv_separator="\t";
            }else{
                $csv_separator=";";   
            }
            $a_temp = explode("\n", $temp);
            $i = 1;
            // BY LINES
            foreach ($a_temp as $k => $v) {
                if ($i > 2) {
                    if (strlen($v) > 5) {
                        $fp = fopen($filename, 'a');
                        $v = "\n" . $title . $csv_separator . $v;
                        fwrite($fp, $v);
                        fclose($fp);
                    }
                }
                $i++;
            }



        }

        if ($this->debug) {
            echo '<hr><div style="font-family: \'Courier New\'; font-size:11px;">';
            echo "\r\n";
            $result = file_get_contents($filename);
            $result = str_replace("\n", "<br>\r\n", $result);
            echo $result;
            echo '</div>';
        } elseif ($this->mode == "download") {
            //CSV HEADERS
            header('Content-Type: application/excel');
            header('Content-Disposition: attachment; filename="' . $now . '.csv"');
            $str = file_get_contents($filename);
            $str = "\xFF\xFE" . iconv("UTF-8", "UCS-2LE", $str);
            die($str);
        }
    }

    /**
     * MODE: download-year -> Send a long csv with all the csv's (just of the current year)
     */
    function download_year()
    {
        $now = date('Y-m-d--His');
        $filename = 'zztodolists--' . $now . '--.csv';

        if (isset($_GET["year"]))
        {
            if (is_numeric($_GET["year"]))
            {
                $year=$_GET["year"];
            }else{
                $year=date("Y");
            }
        }else{
            $year=date("Y");
        }


        // CSV TITLE FIRST LINE
        $fp = fopen($filename, 'a');
        if ($this->detect_utf_encoding($val)=="UTF-16LE")
        {   
            $str = "Project\tTaskID\tTitle\tUserID\tStartDate\tStartTime\tEndDate\tEndTime\tTimeSpent\tComment\tType\tPath";
        }else{
            $str = "Project;TaskID;Title;UserID;StartDate;StartTime;EndDate;EndTime;TimeSpent;Comment;Type;Path";
        }
        fwrite($fp, $str);
        fclose($fp);


        foreach ($this->a_csv as $key => $val) {
            $temp = file_get_contents($val);


            $todotimelog = substr($temp, 2, 21);
            $todotimelog = str_replace(' ', '', $todotimelog);
            $todotimelog = serialize($todotimelog);
            $title = substr($val, strlen($this->logsDir) + 1, -8);

            // CSV IS CODED IN UCS-2 LE BOM
            if ($this->detect_utf_encoding($val)=="UTF-16LE")
            {                
                $temp = iconv('UCS-2LE', 'UTF-8', substr($temp, 0));//last byte was invalid
                $csv_separator="\t";
            }else{
                $csv_separator=";";   
            }
            $a_temp = explode("\n", $temp);
            $i = 1;
            // BY LINES
            foreach ($a_temp as $k => $v) {
                if ($i > 2) {
                    if (strlen($v) > 5) {
                        $a_task=explode($csv_separator,$v);
                        //echo "<pre>".var_dump($a_task)."</pre>";// DEBUG
                        // StartDate=field 3
                        // EndDate=field 5
                        // WorkTime=field 7
                        // FILTER BY END DATE YEAR FIELD
                        if (substr_count($a_task[5],$year)>0) 
                        {
                            // FILTER
                            $fp = fopen($filename, 'a');
                            $v = "\n" . $title . $csv_separator . $v;
                            fwrite($fp, $v);
                            fclose($fp);
                        }
                    }
                }
                $i++;
            }



        }

        if ($this->debug) {
            echo '<hr><div style="font-family: \'Courier New\'; font-size:11px;">';
            echo "\r\n";
            $result = file_get_contents($filename);
            $result = str_replace("\n", "<br>\r\n", $result);
            echo $result;
            echo '</div>';
        } elseif ($this->mode == "download_year") {
            //CSV HEADERS
            header('Content-Type: application/excel');
            header('Content-Disposition: attachment; filename="' . $now . '.csv"');
            $str = file_get_contents($filename);
            $str = "\xFF\xFE" . iconv("UTF-8", "UCS-2LE", $str);
            die($str);
        }
    }

    /**
     * MODE: dokuwiki -> Creates a Dokuwiki table
     */
    function dokuwiki()
    {
        $str  = "|Project|TaskID|Title|UserID|TaskStartDate|StartTime|TaskEndDate|EndTime|TimeSpent|Comment|Type|Path|<br>\r\n";
        $str .= "|-------|------|-----|------|-------------|---------|-----------|-------|---------|-------|----|----|<br>\r\n";
        echo $str;
        foreach ($this->a_csv as $key => $val) {
            $temp = file_get_contents($val);


            $todotimelog = substr($temp, 2, 21);
            $todotimelog = str_replace(' ', '', $todotimelog);
            $todotimelog = serialize($todotimelog);

            $title = substr($val, strlen($this->logsDir) + 1, -8);

            // CSV IS CODED IN UCS-2 LE BOM
            if ($this->detect_utf_encoding($val)=="UTF-16LE")
            {                
                $temp = iconv('UCS-2LE', 'UTF-8', substr($temp, 0));//last byte was invalid
                $csv_separator="\t";
            }else{
                $csv_separator=";";   
            }
            $a_temp = explode("\n", $temp);
            $i = 1;
            // BY LINES
            foreach ($a_temp as $k => $v) {
                if ($i > 2) {
                    if (strlen($v) > 5) {
                        // REPLACING DOKUWIKI NOR COMPATIBLE CHARS
                        $v=str_replace("|","--",$v);
                        $v = "\n" . $title . "\t" . $v;
                        $v=str_replace("\t","|",$v);
                        $v=str_replace("||","| |",$v);
                        $v=str_replace("_"," ",$v);
                        // SPANISH TO ENGLISH
                        $v=str_replace("|Rastreado|","|Tracked|",$v);
                        echo "|".trim($v)."|<br>\r\n";
                    }
                }
                $i++;
            }
            


        }
    }

    /**
     * MODE: screen -> Creates a html table
     */
    function screen()
    {
        $str ='<table border="1" cellspacing="0" cellpadding="0" style="border-color:#f1f1f1; font-family: courier;font-size:10px;">'."\r\n";
        $str .= "<tr><td>Project|TaskID|Title|UserID|TaskStartDate|StartTime|TaskEndDate|EndTime|TimeSpent|Comment|Type|Path</td></tr>\r\n";
        $str .= "<tr><td>-------|------|-----|------|-------------|---------|-----------|-------|---------|-------|----|----</td></tr>\r\n";
        $str=str_replace("|","</td><td>",$str);
        echo $str;
        foreach ($this->a_csv as $key => $val) {
            $temp = file_get_contents($val);


            $todotimelog = substr($temp, 2, 21);
            $todotimelog = str_replace(' ', '', $todotimelog);
            $todotimelog = serialize($todotimelog);
            similar_text($todotimelog, 'TODOTIMELOG', $percent);

            // IS A TODOTIMELOG CSV FILE???
            if ($percent = 55) {

                $title = substr($val, strlen($this->logsDir) + 1, -8);

                // CSV IS CODED IN UCS-2 LE BOM
                if ($this->detect_utf_encoding($val)=="UTF-16LE")
                {                
                    $temp = iconv('UCS-2LE', 'UTF-8', substr($temp, 0));//last byte was invalid
                    $csv_separator="\t";
                }else{
                    $csv_separator=";";   
                }
                $a_temp = explode("\n", $temp);
                $i = 1;
                // BY LINES
                foreach ($a_temp as $k => $v) {
                    if ($i > 2) {
                        if (strlen($v) > 5) {
                            // REPLACING DOKUWIKI NOT COMPATIBLE CHARS
                            $v=str_replace("|","--",$v);
                            $v = "\n" . $title . $csv_separator . $v;
                            $v=str_replace($csv_separator,"|",$v);
                            $v=str_replace("||","| |",$v);
                            $v=str_replace("_"," ",$v);
                            // SPANISH TO ENGLISH
                            $v=str_replace("|Rastreado|","|Tracked|",$v);
                            // TO TABLE
                            $v=str_replace("|","</td><td>",$v);
                            echo "<tr><td>".trim($v)."</td></tr>\r\n";
                        }
                    }
                    $i++;
                }
            }


        }
        echo "</table>\r\n";
    }

    /**
     * MODE: screen_year -> Creates a html table   (current year ended tasks)
     */
    function screen_year($year=0)
    {
        if (isset($_GET["year"]))
        {
            if (is_numeric($_GET["year"]))
            {
                $year=$_GET["year"];
            }else{
                $year=date("Y");
            }
        }else{
            $year=date("Y");
        }

        $workTime=0;
        $str ='<table border="1" cellspacing="0" cellpadding="0" style="border-color:#f1f1f1; font-family: courier;font-size:10px;">'."\r\n";
        $str .= "<tr><td>Project|TaskID|Title|UserID|TaskStartDate|StartTime|TaskEndDate|EndTime|TimeSpent|Comment|Type|Path</td></tr>\r\n";
        $str .= "<tr><td>-------|------|-----|------|-------------|---------|-----------|-------|---------|-------|----|----</td></tr>\r\n";
        $str=str_replace("|","</td><td>",$str);
        echo $str;
        foreach ($this->a_csv as $key => $val) {
            $temp = file_get_contents($val);


            $todotimelog = substr($temp, 2, 21);
            $todotimelog = str_replace(' ', '', $todotimelog);
            $todotimelog = serialize($todotimelog);
            $title = substr($val, strlen($this->logsDir) + 1, -8);

            // CSV IS CODED IN UCS-2 LE BOM
            if ($this->detect_utf_encoding($val)=="UTF-16LE")
            {                
                $temp = iconv('UCS-2LE', 'UTF-8', substr($temp, 0));//last byte was invalid
                $csv_separator="\t";
            }else{
                $csv_separator=";";   
            }
            $a_temp = explode("\n", $temp);
            $i = 1;
            // BY LINES
            foreach ($a_temp as $k => $v) {
                if ($i > 2) {
                    if (strlen($v) > 5) {
                        // REPLACING DOKUWIKI NOT COMPATIBLE CHARS
                        $v=str_replace("|","--",$v);
                        $a_task=explode($csv_separator,$v);
                        
                        // echo "<pre>".var_dump($a_task)."</pre>";// DEBUG
                        // StartDate=field 3
                        // EndDate=field 5
                        // WorkTime=field 7
                        // FILTER BY END DATE YEAR FIELD
                        if (substr_count($a_task[5],$year)>0) 
                        {
                            $workTime+=str_replace(",",".",$a_task[7]);
                            $v = "\n" . $title . $csv_separator . $v;
                            $v=str_replace($csv_separator,"|",$v);
                            $v=str_replace("||","| |",$v);
                            $v=str_replace("_"," ",$v);
                            // SPANISH TO ENGLISH
                            $v=str_replace("|Rastreado|","|Tracked|",$v);
                            // TO TABLE
                            $v=str_replace("|","</td><td>",$v);
                            echo "<tr><td>".trim($v)."</td></tr>\r\n";
                        }                            
                    }
                }
                $i++;
            }


        }
        echo "</table>\r\n";
        echo "workTime=".$workTime;
    }


    /**
     * MODE: screen_year_month -> Creates a html table   (current year-month ended tasks)
     */
    function screen_year_month($year=0)
    {
        if (isset($_GET["year"]))
        {
            if (is_numeric($_GET["year"]))
            {
                $year=$_GET["year"];
            }else{
                $year=date("Y");
            }
        }else{
            $year=date("Y");
        }


        if (isset($_GET["month"]))
        {
            if ( (is_numeric($_GET["month"])) && ($_GET["month"]>0) && ($_GET["month"]<13) )
            {
                $month=$_GET["month"];
                if ( ($month<10) && (substr($month,0,1)!="0") )
                {
                    $month="0".$month;
                }
            }else{
                $month=date("m");
            }
        }else{
            $month=date("m");
        }

    
    

        $workTime=0;
        $str ='<table border="1" cellspacing="0" cellpadding="0" style="border-color:#f1f1f1; font-family: courier;font-size:10px;">'."\r\n";
        $str .= "<tr><td>Project|TaskID|Title|UserID|TaskStartDate|StartTime|TaskEndDate|EndTime|TimeSpent|Comment|Type|Path</td></tr>\r\n";
        $str .= "<tr><td>-------|------|-----|------|-------------|---------|-----------|-------|---------|-------|----|----</td></tr>\r\n";
        $str=str_replace("|","</td><td>",$str);
        echo $str;
        foreach ($this->a_csv as $key => $val) {
            $temp = file_get_contents($val);


            $todotimelog = substr($temp, 2, 21);
            $todotimelog = str_replace(' ', '', $todotimelog);
            $todotimelog = serialize($todotimelog);
 
            $title = substr($val, strlen($this->logsDir) + 1, -8);

            // CSV IS CODED IN UCS-2 LE BOM
            if ($this->detect_utf_encoding($val)=="UTF-16LE")
            {                
                $temp = iconv('UCS-2LE', 'UTF-8', substr($temp, 0));//last byte was invalid
                $csv_separator="\t";
            }else{
                $csv_separator=";";   
            }
            $a_temp = explode("\n", $temp);
            $i = 1;
            // BY LINES
            foreach ($a_temp as $k => $v) {
                if ($i > 2) {
                    if (strlen($v) > 5) {
                        // REPLACING DOKUWIKI NOT COMPATIBLE CHARS
                        $v=str_replace("|","--",$v);
                        $a_task=explode($csv_separator,$v);
                        
                        //echo "<pre>".var_dump($a_task)."</pre>";// DEBUG
                        // StartDate=field 3
                        // EndDate=field 5
                        // WorkTime=field 7
                        // FILTER BY END DATE YEAR FIELD
                        if (substr_count($a_task[5],$year."-".$month)>0) 
                        {
                            $workTime+=str_replace(",",".",$a_task[7]);
                            $v = "\n" . $title . $csv_separator . $v;
                            $v=str_replace($csv_separator,"|",$v);
                            $v=str_replace("||","| |",$v);
                            $v=str_replace("_"," ",$v);
                            // SPANISH TO ENGLISH
                            $v=str_replace("|Rastreado|","|Tracked|",$v);
                            // TO TABLE
                            $v=str_replace("|","</td><td>",$v);
                            echo "<tr><td>".trim($v)."</td></tr>\r\n";
                        }                            
                    }
                }
                $i++;
            }


        }
        echo "</table>\r\n";
        echo "workTime=".$workTime;
    }


    /**
     * MODE: download_year_month -> Send a long csv with all the csv's (year and month)
     */
    function download_year_month()
    {
        $now = date('Y-m-d--His');
        $filename = 'zztodolists--' . $now . '--.csv';

        if (isset($_GET["year"]))
        {
            if (is_numeric($_GET["year"]))
            {
                $year=$_GET["year"];
            }else{
                $year=date("Y");
            }
        }else{
            $year=date("Y");
        }

        if (isset($_GET["month"]))
        {
            if ( (is_numeric($_GET["month"])) && ($_GET["month"]>0) && ($_GET["month"]<13) )
            {
                $month=$_GET["month"];
                if ( ($month<10) && (substr($month,0,1)!="0") )
                {
                    $month="0".$month;
                }
            }else{
                $month=date("m");
            }
        }else{
            $month=date("m");
        }



        // CSV TITLE FIRST LINE
        $fp = fopen($filename, 'a');
        if ($this->detect_utf_encoding($val)=="UTF-16LE")
        {   
            $str = "Project\tTaskID\tTitle\tUserID\tStartDate\tStartTime\tEndDate\tEndTime\tTimeSpent\tComment\tType\tPath";
        }else{
            $str = "Project;TaskID;Title;UserID;StartDate;StartTime;EndDate;EndTime;TimeSpent;Comment;Type;Path";
        }
        fwrite($fp, $str);
        fclose($fp);


        foreach ($this->a_csv as $key => $val) {
            $temp = file_get_contents($val);


            $todotimelog = substr($temp, 2, 21);
            $todotimelog = str_replace(' ', '', $todotimelog);
            $todotimelog = serialize($todotimelog);

            $title = substr($val, strlen($this->logsDir) + 1, -8);

            // CSV IS CODED IN UCS-2 LE BOM
            if ($this->detect_utf_encoding($val)=="UTF-16LE")
            {                
                $temp = iconv('UCS-2LE', 'UTF-8', substr($temp, 0));//last byte was invalid
                $csv_separator="\t";
            }else{
                $csv_separator=";";   
            }
            $a_temp = explode("\n", $temp);
            $i = 1;
            // BY LINES
            foreach ($a_temp as $k => $v) {
                if ($i > 2) {
                    if (strlen($v) > 5) {
                        $a_task=explode($csv_separator,$v);
                        //echo "<pre>".var_dump($a_task)."</pre>";// DEBUG
                        // StartDate=field 3
                        // EndDate=field 5
                        // WorkTime=field 7
                        // FILTER BY END DATE YEAR FIELD
                        if (substr_count($a_task[5],$year."-".$month)>0) 
                        {
                            // FILTER
                            $fp = fopen($filename, 'a');
                            $v = "\n" . $title . $csv_separator . $v;
                            fwrite($fp, $v);
                            fclose($fp);
                        }
                    }
                }
                $i++;
            }
            


        }

        if ($this->debug) {
            echo '<hr><div style="font-family: \'Courier New\'; font-size:11px;">';
            echo "\r\n";
            $result = file_get_contents($filename);
            $result = str_replace("\n", "<br>\r\n", $result);
            echo $result;
            echo '</div>';
        } elseif ($this->mode == "download_year_month") {
            //CSV HEADERS
            header('Content-Type: application/excel');
            header('Content-Disposition: attachment; filename="' . $now . '.csv"');
            $str = file_get_contents($filename);
            $str = "\xFF\xFE" . iconv("UTF-8", "UCS-2LE", $str);
            die($str);
        }
    }


    /**
     * Show help on browser
     */
    function help()
    {
        echo '<html>
        <head>
           <style type="text/css"><!--
              html { 
                  font-size: 12px!important; 
                  font-family: courier new;
              }
              a {
                  font-size: 12px!important; 
                  font-family: courier new;
     
                  text-decoration:none;
                  color:blue;
              }
              a:hover {
                  color:red;
              }
              td {
                font-size: 12px!important; 
                font-family: courier new;
              }
                   --></style>
        </head>
        <body>';
        echo "<h2>todolist-web <small>v".$this->version."</small></h2>"."\r\n";
        echo '<table border="0" cellspacing="0" cellpadding="2" style="border-color:#f1f1f1; font-family: courier;font-size:15px;">'."\r\n";
        // SCREEN        
        echo '<tr><td><a href="?mode=screen" target="_blank"><b>screen</b></a>'." </td><td> HTML table with all the tasks. </td></tr>\r\n";
        echo '<tr><td><a href="?mode=screen_year&year='.date("Y").'" target="_blank"><b>screen_year</b></a>'." </td><td> HTML table with all the tasks ended on a year. </td></tr>\r\n";
        echo '<tr><td><a href="?mode=screen_year_month&year='.date("Y").'&month='.date("m").'" target="_blank"><b>screen_year_month</b></a>'." </td><td> HTML table with all the tasks ended on a year and month. </td></tr>\r\n";
        // DOWNLOAD
        echo '<tr><td><a href="?mode=download" target="_blank"><b>download</b></a>'." </td><td> Download a CSV with all the tasks. </td></tr>\r\n";
        echo '<tr><td><a href="?mode=download_year&year='.date("Y").'" target="_blank"><b>download_year</b></a>'." </td><td> Download a CSV with all the tasks ended on a year. </td></tr>\r\n";
        echo '<tr><td><a href="?mode=download_year_month&year='.date("Y").'&month='.date("m").'" target="_blank"><b>download_year_month</b></a>'." </td><td> Download a CSV with all the tasks ended on a year and month. </td></tr>\r\n";
        // DOKUWIKI
        echo '<tr><td><a href="?mode=dokuwiki" target="_blank"><b>dokuwiki</b></a>'." </td><td> Creates a DokuWiki table with all the tasks. </td></tr>\r\n";
        // listCSV - listTDL
        echo '<tr><td><a href="?mode=listcsv" target="_blank"><b>listcsv</b></a>'." </td><td> List all files (*.csv) </td></tr>\r\n";
        echo '<tr><td><a href="?mode=listtdl" target="_blank"><b>listtdl</b></a>'." </td><td> List all files (*.tdl) </td></tr>\r\n";
        echo '<tr><td><a href="?mode=listhtml" target="_blank"><b>listhtml</b></a>'." </td><td> List all files (*.html) </td></tr>\r\n";
        echo '</table>';

        echo '<br><br><br><a href="http://www.abstractspoon.com" target="_blank"><small>Download ToDoList v8.2<small></a>';
    }

}