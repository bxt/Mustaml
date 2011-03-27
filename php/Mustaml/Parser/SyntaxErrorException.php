<?php
namespace Mustaml\Parser;

/**
 * Exception thrown when there is unparsable stuff in your template string
 * 
 * The error can't be fixed by Mustaml and is detected at parse time. 
 */
class SyntaxErrorException extends \Exception {}