---
title: 'Chat with summary'
docname: 'chat_with_summary'
---

## Overview


## Example


```php
<?php

use Cognesy\Instructor\Extras\Chat\ChatWithSummary;
use Cognesy\Instructor\Extras\Chat\Utils\SummarizeMessages;
use Cognesy\Instructor\Features\LLM\Inference;
use Cognesy\Instructor\Features\LLM\LLM;
use Cognesy\Instructor\Utils\Debug\Debug;
use Cognesy\Instructor\Utils\Messages\Message;
use Cognesy\Instructor\Utils\Messages\Messages;
use Cognesy\Instructor\Utils\Messages\Script;

$loader = require 'vendor/autoload.php';
$loader->add('Cognesy\\Instructor\\', __DIR__ . '../../src/');

$maxSteps = 10;
$sys = [
    'You are helpful assistant explaining Challenger Sale method, you answer questions. Provide very brief answers, not more than one sentence. Simplify things, don\'t go into details, but be very pragmatic and focused on practical bizdev problems.',
    'You are curious novice growth expert interested in improving promoting Instructor library, you keep asking questions. Ask short, simple questions. Always ask a single question. Use your knowledge of Instructor library and marketing of tech products for developers.',
];
$startMessage = new Message('assistant', 'Help me get better sales results. Be brief and concise.');

$readme = "# CONTEXT\n\n" . file_get_contents(__DIR__ . '/summary.md');

$summarizer = new SummarizeMessages(
    //prompt: 'Summarize the messages.',
    llm: LLM::connection('deepseek'),
    //model: 'gpt-4o-mini',
    tokenLimit: 1024,
);

$chat = new ChatWithSummary(
    null,
    256,
    256,
    1024,
    true,
    true,
);
$chat->script()->section('main')->appendMessage($startMessage);

//Debug::enable();

for($i = 0; $i < $maxSteps; $i++) {
    $chat->script()
        ->section('system')
        ->withMessages(Messages::fromString($sys[$i % 2], 'system'))
        ->appendMessage(Message::fromString($readme, 'system'));
    $messages = $chat->script()
        ->select(['system','summary','buffer','main'])
        ->toMessages()
        ->remapRoles(['assistant' => 'user', 'user' => 'assistant', 'system' => 'system']);

    dump($messages->toRoleString());

    $response = Inference::text(
        messages: $messages->toArray(),
        connection: 'deepseek',
        options: ['max_tokens' => 256],
    );

    echo "\n";
    dump('>>> '.$response);
    echo "\n";
    $chat->appendMessage(new Message(role: 'assistant', content: $response));
}
//dump($chat->script());
