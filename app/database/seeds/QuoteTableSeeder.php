<?php

class QuoteTableSeeder extends Seeder
{

    public function run()
    {

        # drop all quotes

        DB::table('quotes')->delete();

        # get quotes json from main quote spreadsheet

        $url = 'http://spreadsheets.google.com/feeds/list/1Xqp_INZG92NUKcL6F9BwcPrDpht0XNLdYLLugZhATbM/od6/public/values?alt=json';
        $file = file_get_contents($url);
        $json = json_decode($file);

        # parse quotes one by one
        $rows = $json->{'feed'}->{'entry'};
        foreach($rows as $row) {
            DB::table('quotes')->insert(
                array(
                    'quote' => $row->{'gsx$quote'}->{'$t'},
                    'author' => $row->{'gsx$author'}->{'$t'},
                    'type' => $row->{'gsx$type'}->{'$t'},
                    'language' => $row->{'gsx$language'}->{'$t'}
                )
            );
        }
    }

}


