<?php

/*
	GitHub: https://github.com/matheusjohannaraujo/makemvcss
	Country: Brasil
	State: Pernambuco
	Developer: Matheus Johann Araujo
	Date: 2021-04-17
*/

/**
 * 
 * **Function -> var_export_format**
 *
 * EN-US: Returns the output of a pre-formatted `var_export`.
 * 
 * PT-BR: Retorna a saída de um `var_export` pré-formatado.
 * 
 * @param mixed &$data [reference variable]
 * @return string
 */
function var_export_format(&$data)
{
    $dump = var_export($data, true);
    $dump = preg_replace('#(?:\A|\n)([ ]*)array \(#i', '[', $dump); // Starts
    $dump = preg_replace('#\n([ ]*)\),#', "\n$1],", $dump); // Ends
    $dump = preg_replace('#=> \[\n\s+\],\n#', "=> [],\n", $dump); // Empties
    if (gettype($data) == 'object') { // Deal with object states
        $dump = str_replace('__set_state(array(', '__set_state([', $dump);
        $dump = preg_replace('#\)\)$#', "])", $dump);
    } else {
        $dump = preg_replace('#\)$#', "]", $dump);
    }
    return $dump;
}

/**
 * 
 * **Function -> dumpl**
 *
 * EN-US: Prints on the screen the values ​​that were passed in the parameters.
 * 
 * PT-BR: Imprime na tela os valores que foram passados ​​nos parâmetros.
 * 
 * @param mixed ...$params [optional]
 * @return null
 */
function dumpl(...$params)
{
    // $params = func_get_args();
    $style = "font-weight:bolder;font-size:1.2em;color:#ccc;background:#333;border-radius:3px;padding:15px;margin:0;display:inline-block;";
    if (!empty($params) > 0) {
        echo !defined('CLI') ? "\r\n<hr/>\r\n" : "";
    }    
    foreach ($params as $key => $value) {
        echo !defined('CLI') ? "<pre style=\"${style}\">\r\n" : "";
        echo var_export_format($value);
        echo !defined('CLI') ? "\r\n</pre>\r\n<hr/>\r\n" : "";
        unset($params[$key]);
    }
    unset($params);
}

/**
 * 
 * **Function -> dumpd**
 *
 * EN-US: Print the values ​​that were passed in the parameters
 * on the screen and end the execution of the php code.
 * 
 * PT-BR: Imprime os valores que foram passados ​​nos parâmetros
 * na tela e finaliza a execução do código php.
 * 
 * @param mixed ...$params [optional]
 * @return null
 */
function dumpd(...$params)
{
    dumpl(...$params);
    die();
}
