<?php


namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;



class Mail
{
    private $api_key = '99c8f154a042a05dfcdb36899123b73b';
    private $api_key_secret = '95046e736c26a380bdf0def8846ebbf1';

    public function send($to_email, $to_name, $subject, $content)
    {


        $mj = new Client($this->api_key, $this->api_key_secret, true, ['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "fafa.afpa@gmail.com",
                        'Name' => "GREEN MIND"
                    ],
                    'To' => [
                        [
                            'Email' =>  $to_email,
                            'Name' =>  $to_name
                        ]
                    ],
                    'TemplateID' => 3569364,
                    'Subject' => $subject,
                    'TemplateLanguage' => true,
                    'Variables' => [
                        'content' => $content
                    ]


                ]
            ]
        ];

        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success();
    }
}
