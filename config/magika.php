<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Magika Binary Path
    |--------------------------------------------------------------------------
    |
    | The path to the Magika CLI binary. If null, the package will look for
    | "magika" in your system PATH. You can install it via:
    |   brew install magika
    |   pipx install magika
    |   cargo install --locked magika-cli
    |
    */

    'binary_path' => env('MAGIKA_BINARY_PATH', null),

    /*
    |--------------------------------------------------------------------------
    | Prediction Mode
    |--------------------------------------------------------------------------
    |
    | Controls the tolerance to errors. Available modes:
    | - "high-confidence": Only returns a label if the model is highly confident.
    | - "medium-confidence": Returns a label with medium confidence threshold.
    | - "best-guess": Always returns the model's best guess.
    |
    */

    'prediction_mode' => env('MAGIKA_PREDICTION_MODE', 'high-confidence'),

    /*
    |--------------------------------------------------------------------------
    | Process Timeout
    |--------------------------------------------------------------------------
    |
    | Maximum time in seconds the Magika process is allowed to run.
    |
    */

    'timeout' => env('MAGIKA_TIMEOUT', 30),

];
