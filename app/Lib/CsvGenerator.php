<?php namespace App\Lib;


class CsvGenerator
{

    /**
     * Generate CSV file
     * @param string $org_id
     * @param $headers
     * @param string $filename
     */
    public function create($dataset=[], $headers=[], $filename=''){
        // Init file name
        $file_name = "";
        // Init data
        $data = [];

        // Set file name
        if($filename != '' and strpos($filename, ".csv") !== false){
            $file_name = $filename;
        }else{
            // Create random file name
            $file_name = date('YmdHis').'.csv';
        }

        //output headers so that the file is downloaded rather than displayed
        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=$file_name");
        //Disable caching - HTTP 1.1
        header("Cache-Control: no-cache, no-store, must-revalidate");
        //Disable caching - HTTP 1.0
        header("Pragma: no-cache");
        //Disable caching - Proxies
        header("Expires: 0");

        //Start the ouput
        $output = fopen("php://output", "w");

        // Set headers
        if(!empty($headers)){
            $data[] = $headers;
        }

        //set data
        foreach($dataset as $row){
            $data[] = $row;
        }

        //Then loop through the rows
        foreach ($data as $row) {
            //Add the rows to the body
            fputcsv($output, $row); // here you can change delimiter/enclosure
        }

        // Close the stream off
        fclose($output);
    }

}