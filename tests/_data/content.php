<?php
return [
    [
        'id' => 'content_text_1',
        'type' => 'TEXT',
        'next_content_id' => 'content_delay_1',
    ],
    [
        'id' => 'content_delay_1',
        'type' => 'DELAY',
        'next_content_id' => 'content_text_2',
    ],
    [
        'id' => 'content_text_2',
        'type' => 'TEXT',
        'next_content_id' => null,
    ],
];
