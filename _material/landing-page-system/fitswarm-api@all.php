<?php

if (strlen($input->post('plugin')) > 0) {
    $curl = new Curl;

    switch ($input->post('plugin')) {
        case 'instructor-join':
            $url  = 'https://www.fitswarm.com/api/v1/landing/instructor-join';
            $data = [
                'key' => $page->integration_api_idstudio,
                'first-name' => $input->post('first-name'),
                'last-name' => $input->post('last-name'),
                'email' => $input->post('email'),
                'age' => $input->post('age')
            ];
            break;
        case 'member-join-book':
            $url  = 'https://www.fitswarm.com/api/v1/landing/member-join-book';
            $data = [
                'key' => $page->integration_api_idstudio,
                'first-name' => $input->post('first-name'),
                'last-name' => $input->post('last-name'),
                'email' => $input->post('email'),
                'age' => $input->post('age'),
                'class-id' => $input->post('class-id')
            ];
            break;
    }

    $response = $curl->post($url, $data);
    $responseArray = json_decode($response);

    if ($responseArray->status == 'error') {
    	$message = $page->form_message_error . '<br>';

    	foreach ($responseArray->errors as $error) {
    		$message .= $error->message;
    	}

    	$success = false;
    } elseif ($responseArray->status == 'success' && strlen($responseArray->url) > 0) {
        $session->redirect($responseArray->url);
    }
}
