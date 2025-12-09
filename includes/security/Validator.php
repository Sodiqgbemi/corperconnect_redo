<?php

declare(strict_types=1);

namespace Includes\Security;

use InvalidArgumentException;

/**
 * Validator Class
 * 
 * Provides comprehensive input validation and sanitization
 * with support for custom rules and error messages.
 * 
 * @package Includes\Security
 */
class Validator
{
    // Error message format constants
    public const PLAIN_ERRORMSGS = 0;
    public const FIELDS_AND_PLAIN_ERRORMSGS = 1;
    public const HTML_ERRORMSGS = 2;
    public const FIELDS_AND_HTML_ERRORMSGS = 3;
    public const FORCE_FULLSTOP_ON_ERRORMSGS = true;

    // Filter flag aliases mapping
    protected array $_aliases = [
        'allow_fraction' => FILTER_FLAG_ALLOW_FRACTION,
        'allow_hex' => FILTER_FLAG_ALLOW_HEX,
        'allow_octal' => FILTER_FLAG_ALLOW_OCTAL,
        'allow_scientific' => FILTER_FLAG_ALLOW_SCIENTIFIC,
        'allow_thousand' => FILTER_FLAG_ALLOW_THOUSAND,
        'alphabet' => 'alphabetic',
        'bool' => 'boolean',
        'casttonum' => 'casttonumeric',
        'casttonumber' => 'casttonumeric',
        'defaultphp' => 'defaultspecial',
        'encode_amp' => FILTER_FLAG_ENCODE_AMP,
        'encode_high' => FILTER_FLAG_ENCODE_HIGH,
        'encode_low' => FILTER_FLAG_ENCODE_LOW,
        'fileext' => 'fileextension',
        'inlist' => 'inlistci',
        'int' => 'integer',
        'ipv4' => FILTER_FLAG_IPV4,
        'ipv6' => FILTER_FLAG_IPV6,
        'path_required' => FILTER_FLAG_PATH_REQUIRED,
        'query_required' => FILTER_FLAG_QUERY_REQUIRED,
        'no_encode_quotes' => FILTER_FLAG_NO_ENCODE_QUOTES,
        'no_priv_range' => FILTER_FLAG_NO_PRIV_RANGE,
        'no_res_range' => FILTER_FLAG_NO_RES_RANGE,
        'notinlist' => 'notinlistci',
        'null_on_failure' => FILTER_NULL_ON_FAILURE,
        'num' => 'numeric',
        'number' => 'numeric',
        'str' => 'string',
        'strip_high' => FILTER_FLAG_STRIP_HIGH,
        'strip_low' => FILTER_FLAG_STRIP_LOW,
        'strip_backtick' => FILTER_FLAG_STRIP_BACKTICK,
    ];

    protected string $_argsDelimiter = ', ';
    protected array $_customSanitizations = [];
    protected array $_customValidations = [];
    protected string $_fieldHierarchyDelimiter = '.';
    protected string $_fieldLabelFwdHierarchyDelimiter = '.';
    protected string $_fieldLabelRevHierarchyDelimiter = ' of ';
    protected bool $_mbSupported = false;

    // Default validation error messages
    protected array $_factoryValidationErrorMsgs = [
        'default' => '<b>{field}</b> is invalid',
        'default_long' => 'Field <b>{field}</b> with value \'{value}\' failed validation {filter}',
        'inexistent_validation' => 'Validation filter {filter} does not exist for <b>{field}</b>. Contact admin.',
        'alphabetic' => '<b>{field}</b> may only contain alphabetic characters',
        'alphanumeric' => '<b>{field}</b> may only contain alpha-numeric characters',
        'boolean' => '<b>{field}</b> may only contain a true or false value',
        'creditcard' => '<b>{field}</b> does not contain a valid credit card number',
        'date' => '<b>{field}</b> is not a valid date',
        'email' => '<b>{field}</b> is not a valid email address',
        'empty' => '<b>{field}</b> must be empty',
        'endswith' => '<b>{field}</b> does not end with {arg1}',
        'equalsfield' => '<b>{field}</b> does not equal {arg1}',
        'exactlen' => '<b>{field}</b> must be exactly {arg1} characters long',
        'fail' => '<b>{field}</b> failed server validation',
        'fileextension' => '<b>{field}</b> does not have a valid file extension',
        'float' => '<b>{field}</b> may only contain a float value',
        'guidv4' => '<b>{field}</b> is not a valid GUID (v4)',
        'iban' => '<b>{field}</b> is not a valid IBAN',
        'inlistci' => '<b>{field}</b> must be one of these values: {args}',
        'inlistcs' => '{copy:inlistci}',
        'integer' => '<b>{field}</b> may only contain an integer value',
        'ip' => '<b>{field}</b> does not contain a valid IP address',
        'ipv4' => '<b>{field}</b> does not contain a valid IPv4 address',
        'ipv6' => '<b>{field}</b> does not contain a valid IPv6 address',
        'jsonstring' => '<b>{field}</b> is not a JSON-encoded string',
        'maxlen' => '<b>{field}</b> must be {arg1} or shorter in length',
        'maxnumeric' => '<b>{field}</b> must be a numeric value, equal to or lower than {arg1}',
        'minage' => 'The <b>{field}</b> field needs to have an age greater than or equal to {arg1}',
        'minlen' => '<b>{field}</b> must be {arg1} or longer in length',
        'minnumeric' => 'The <b>{field}</b> field needs to be a numeric value, equal to, or higher than {arg1}',
        'mismatch' => 'There is no validation rule for <b>{field}</b>',
        'notempty' => '<b>{field}</b> cannot be empty',
        'notinlistci' => '<b>{field}</b> cannot be one of these values: {args}',
        'notinlistcs' => '{copy:notinlistci}',
        'numeric' => '<b>{field}</b> may only contain numeric characters',
        'pass' => 'Placeholder text, will never be used as {filter} will never fail! :)',
        'personname' => '<b>{field}</b> does not seem to contain a person\'s name',
        'phonenumber' => '<b>{field}</b> does not seem to contain a valid phone number',
        'regex' => '<b>{field}</b> did not match regular expression: {arg1}',
        'required' => '<b>{field}</b> is required',
        'requiredfile' => 'File is required for <b>{field}</b>',
        'startswith' => '<b>{field}</b> does not start with {arg1}',
        'streetaddress' => '<b>{field}</b> does not seem to be a valid street address',
        'url' => 'The <b>{field}</b> field is required to be a valid URL',
        'urlexists' => '<b>{field}</b> URL does not exist',
    
    ];

    protected array $_emptyErrormsgHTMLSpanAttr = [
        'errormsg' => '',
        'field' => '',
        'value' => '',
        'filter' => '',
        'arg' => '',
    ];

    protected array $_errormsgHTMLSpanAttr = [];
    protected array $_filters = [];
    protected array $_validationErrorLog = [];

    protected string $_basicHTMLTags = '<b><blockquote><br><code><dd><dl>'
        . '<em><hr><h1><h2><h3><h4><h5><h6><i><img><label><li><p><span>'
        . '<strong><sub><sup><ul>';

    protected string $_enNoiseWords = 'about,after,all,also,an,and,another,any,'
        . 'are,as,at,be,because,been,before,being,between,both,but,by,came,'
        . 'can,come,could,did,do,each,for,from,get, got,has,had,he,have,'
        . 'her,here,him,himself,his,how,if,in,into,is,it,its,it\'s,like,'
        . 'make,many,me,might,more,most,much,must,my,never,now,of,on,only,'
        . 'or,other,our,out,over,said,same,see,should,since,some,still,'
        . 'such,take,than,that,the,their,them,then,there,these,they,this,'
        . 'those,through,to,too,under,up,very,was,way,we,well,were,what,'
        . 'where,which,while,who,with,would,you,your,a,b,c,d,e,f,g,h,i,j,k,'
        . 'l,m,n,o,p,q,r,s,t,u,v,w,x,y,z,$,1,2,3,4,5,6,7,8,9,0,_';

    /**
     * Constructor
     *
     * @param array $filters Field configuration array
     */
    public function __construct(array $filters = [])
    {
        $this->_mbSupported = function_exists('mb_strlen');
        $this->_errormsgHTMLSpanAttr = $this->_emptyErrormsgHTMLSpanAttr;

        if (!empty($filters)) {
            $this->setFieldFilters($filters);
        }
    }

    /**
     * Set field filters for validation and sanitization
     *
     * @param array $filters Field configuration
     * @return self
     */
    public function setFieldFilters(array $filters): self
    {
        foreach ($filters as $field => $config) {
            if (!is_array($config)) {
                throw new InvalidArgumentException("Filter configuration for field '{$field}' must be an array");
            }

            // Normalize key names (support both singular and plural)
            if (isset($config['validation']) && !isset($config['validations'])) {
                $config['validations'] = $config['validation'];
                unset($config['validation']);
            }

            if (isset($config['sanitization']) && !isset($config['sanitizations'])) {
                $config['sanitizations'] = $config['sanitization'];
                unset($config['sanitization']);
            }

            $this->_filters[$field] = $config;
            $this->_setFieldLabelAndHierarchy($field);
        }

        return $this;
    }

    /**
     * Run validation and sanitization on input data
     *
     * @param array $input Input data to validate
     * @return array|false Sanitized data on success, false on validation failure
     */
    public function run(array $input)
    {
        $this->_validationErrorLog = [];
        $sanitizedData = [];

        foreach ($this->_filters as $field => $config) {
            $value = $input[$field] ?? null;

            // Apply sanitization first
            if (isset($config['sanitizations'])) {
                $value = $this->_applySanitizations($field, $value, $config['sanitizations']);
            }

            // Then apply validation
            if (isset($config['validations'])) {
                if (!$this->_applyValidations($field, $value, $config['validations'])) {
                    // Validation failed, continue to collect all errors
                    continue;
                }
            }

            $sanitizedData[$field] = $value;
        }

        // Return false if there are validation errors
        return empty($this->_validationErrorLog) ? $sanitizedData : false;
    }

    /**
     * Get validation errors
     *
     * @param int $format Error format (use class constants)
     * @return array Validation errors
     */
    public function getValidationErrors(int $format = self::PLAIN_ERRORMSGS): array
    {
        $errors = [];

        foreach ($this->_validationErrorLog as $field => $error) {
            $message = $this->_formatErrorMessage($field, $error, $format);
            
            switch ($format) {
                case self::FIELDS_AND_PLAIN_ERRORMSGS:
                case self::FIELDS_AND_HTML_ERRORMSGS:
                    $errors[$field] = $message;
                    break;
                default:
                    $errors[] = $message;
                    break;
            }
        }

        return $errors;
    }

    /**
     * Apply sanitizations to a field value
     *
     * @param string $field Field name
     * @param mixed $value Field value
     * @param string $sanitizations Pipe-separated sanitization rules
     * @return mixed Sanitized value
     */
    private function _applySanitizations(string $field, $value, string $sanitizations)
    {
        $filters = explode('|', $sanitizations);

        foreach ($filters as $filter) {
            $filter = trim($filter);
            if (empty($filter)) {
                continue;
            }

            // Parse filter and arguments
            [$filterName, $args] = $this->_parseFilter($filter);

            // Apply sanitization
            $value = $this->_executeSanitization($filterName, $value, $args);
        }

        return $value;
    }

    /**
     * Apply validations to a field value
     *
     * @param string $field Field name
     * @param mixed $value Field value
     * @param string $validations Pipe-separated validation rules
     * @return bool True if all validations pass
     */
    private function _applyValidations(string $field, $value, string $validations): bool
    {
        $filters = explode('|', $validations);

        foreach ($filters as $filter) {
            $filter = trim($filter);
            if (empty($filter)) {
                continue;
            }

            // Parse filter and arguments
            [$filterName, $args] = $this->_parseFilter($filter);

            // Execute validation
            if (!$this->_executeValidation($field, $value, $filterName, $args)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Parse filter string into name and arguments
     *
     * @param string $filter Filter string (e.g., "maxlen:10" or "regex:/pattern/")
     * @return array [filterName, arguments]
     */
    private function _parseFilter(string $filter): array
    {
        // Check for arguments separated by colon or comma
        if (strpos($filter, ':') !== false) {
            $parts = explode(':', $filter, 2);
            $filterName = trim($parts[0]);
            $args = !empty($parts[1]) ? array_map('trim', explode(',', $parts[1])) : [];
        } elseif (strpos($filter, ',') !== false) {
            $parts = array_map('trim', explode(',', $filter));
            $filterName = array_shift($parts);
            $args = $parts;
        } else {
            $filterName = $filter;
            $args = [];
        }

        // Apply aliases
        $filterName = $this->_aliases[$filterName] ?? $filterName;

        return [$filterName, $args];
    }

    /**
     * Execute a sanitization filter
     *
     * @param string $filterName Sanitization filter name
     * @param mixed $value Value to sanitize
     * @param array $args Filter arguments
     * @return mixed Sanitized value
     */
    private function _executeSanitization(string $filterName, $value, array $args)
    {
        // Check for custom sanitization
        if (isset($this->_customSanitizations[$filterName])) {
            return call_user_func($this->_customSanitizations[$filterName], $value, $args);
        }

        // Check for built-in sanitization method
        $method = 'sanitize_' . $filterName;
        if (method_exists($this, $method)) {
            return $this->$method($value, $args);
        }

        // Return value unchanged if sanitization not found
        return $value;
    }

    /**
     * Execute a validation filter
     *
     * @param string $field Field name
     * @param mixed $value Value to validate
     * @param string $filterName Validation filter name
     * @param array $args Filter arguments
     * @return bool True if validation passes
     */
    private function _executeValidation(string $field, $value, string $filterName, array $args): bool
    {
        $result = false;

        // Check for custom validation
        if (isset($this->_customValidations[$filterName])) {
            $result = call_user_func($this->_customValidations[$filterName], $value, $args);
        } else {
            // Check for built-in validation method
            $method = 'validate_' . $filterName;
            if (method_exists($this, $method)) {
                $result = $this->$method($value, $args);
            } else {
                // Log error for inexistent validation
                $this->_logValidationError($field, $value, 'inexistent_validation', [$filterName]);
                return false;
            }
        }

        // Log error if validation failed
        if (!$result) {
            $this->_logValidationError($field, $value, $filterName, $args);
        }

        return $result;
    }

    /**
     * Log a validation error
     *
     * @param string $field Field name
     * @param mixed $value Field value
     * @param string $filter Filter that failed
     * @param array $args Filter arguments
     */
    private function _logValidationError(string $field, $value, string $filter, array $args = []): void
    {
        $this->_validationErrorLog[$field] = [
            'value' => $value,
            'filter' => $filter,
            'args' => $args,
            'errormsg' => $this->_getErrorMessage($filter),
        ];
    }

    /**
     * Get error message template for a filter
     *
     * @param string $filter Filter name
     * @return string Error message template
     */
    private function _getErrorMessage(string $filter): string
    {
        // Check for custom error message in filter config
        // Fall back to factory message
        return $this->_factoryValidationErrorMsgs[$filter] ?? $this->_factoryValidationErrorMsgs['default'];
    }

    /**
     * Format error message with replacements
     *
     * @param string $field Field name
     * @param array $error Error data
     * @param int $format Output format
     * @return string Formatted error message
     */
    private function _formatErrorMessage(string $field, array $error, int $format): string
    {
        $message = $error['errormsg'];
        $value = $error['value'];
        $filter = $error['filter'];
        $args = $error['args'];

        // Get field label
        $label = $this->_filters[$field]['label'] ?? $this->_generateFieldLabel($field);

        // Prepare replacements
        $replacements = [
            '{field}' => $label,
            '{value}' => $value === null || $value === '' ? 'empty' : (is_array($value) ? implode(', ', $value) : $value),
            '{filter}' => $filter,
            '{args}' => implode($this->_argsDelimiter, $args),
        ];

        // Add individual argument replacements
        foreach ($args as $index => $arg) {
            $replacements['{arg' . ($index + 1) . '}'] = $arg;
        }

        // Apply replacements
        $message = str_replace(array_keys($replacements), array_values($replacements), $message);

        // Strip HTML for plain text format
        if ($format === self::PLAIN_ERRORMSGS || $format === self::FIELDS_AND_PLAIN_ERRORMSGS) {
            $message = strip_tags($message);
        }

        return $message;
    }

    /**
     * Set field label and hierarchy
     *
     * @param string $field Field name
     */
    private function _setFieldLabelAndHierarchy(string $field): void
    {
        if (!isset($this->_filters[$field]['field']) || !isset($this->_filters[$field]['fieldLineage'])) {
            $fieldHierarchy = explode($this->_fieldHierarchyDelimiter, $field);
            $fieldHierarchyMaxDepth = count($fieldHierarchy) - 1;

            $this->_filters[$field]['fieldLineage'] = [];
            foreach ($fieldHierarchy as $depth => $node) {
                if ($depth == $fieldHierarchyMaxDepth) {
                    $this->_filters[$field]['field'] = $node;
                } else {
                    $this->_filters[$field]['fieldLineage'][] = $node;
                }
            }
        }

        if (!isset($this->_filters[$field]['label']) || $this->_filters[$field]['label'] == '') {
            $this->_filters[$field]['label'] = $this->_generateFieldLabel($field);
        }
    }

    /**
     * Generate a human-readable label from field name
     *
     * @param string $field Field name
     * @return string Generated label
     */
    private function _generateFieldLabel(string $field): string
    {
        $fieldName = $this->_filters[$field]['field'] ?? $field;
        
        // Convert snake_case and camelCase to Title Case
        $label = preg_replace('/[_-]+/', ' ', $fieldName);
        $label = preg_replace('/([a-z])([A-Z])/', '$1 $2', $label);
        $label = ucwords(strtolower($label));

        return $label;
    }

    /**
     * Add custom validation rule
     *
     * @param string $name Validation name
     * @param callable $callback Validation callback
     * @param string $errorMsg Optional error message
     * @return self
     */
    public function addCustomValidation(string $name, callable $callback, string $errorMsg = ''): self
    {
        $this->_customValidations[$name] = $callback;
        
        if (!empty($errorMsg)) {
            $this->_factoryValidationErrorMsgs[$name] = $errorMsg;
        }

        return $this;
    }

    /**
     * Add custom sanitization rule
     *
     * @param string $name Sanitization name
     * @param callable $callback Sanitization callback
     * @return self
     */
    public function addCustomSanitization(string $name, callable $callback): self
    {
        $this->_customSanitizations[$name] = $callback;
        return $this;
    }

    // ==================== VALIDATION METHODS ====================

    /**
     * Validate alphabetic characters only
     */
    protected function validate_alphabetic($value, $args = null): bool
    {
        return (bool) preg_match('/^[a-zA-Z]+$/', (string) $value);
    }

    /**
     * Validate alphanumeric characters only
     */
    protected function validate_alphanumeric($value, $args = null): bool
    {
        return (bool) preg_match('/^[a-zA-Z0-9]+$/', (string) $value);
    }

    /**
     * Validate boolean value
     */
    protected function validate_boolean($value, $args = null): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null;
    }

    /**
     * Validate credit card number using Luhn algorithm
     */
    protected function validate_creditcard($value, $args = null): bool
    {
        $value = preg_replace('/\D/', '', (string) $value);
        
        if (strlen($value) < 13 || strlen($value) > 19) {
            return false;
        }

        $sum = 0;
        $numDigits = strlen($value);
        $parity = $numDigits % 2;

        for ($i = 0; $i < $numDigits; $i++) {
            $digit = (int) $value[$i];
            
            if ($i % 2 == $parity) {
                $digit *= 2;
            }
            
            if ($digit > 9) {
                $digit -= 9;
            }
            
            $sum += $digit;
        }

        return ($sum % 10) === 0;
    }

    /**
     * Validate date format
     */
    protected function validate_date($value, $args = null): bool
    {
        $format = $args[0] ?? 'Y-m-d';
        $date = \DateTime::createFromFormat($format, (string) $value);
        return $date && $date->format($format) === $value;
    }

    /**
     * Validate email address
     */
    protected function validate_email($value, $args = null): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate field is empty
     */
    protected function validate_empty($value, $args = null): bool
    {
        return $value === null || $value === '';
    }

    /**
     * Validate field ends with specific string
     */
    protected function validate_endswith($value, $args = null): bool
    {
        if (empty($args)) {
            return true;
        }

        $needle = $args[0];
        $caseInsensitive = isset($args[1]) && $args[1] === 'caseinsensitive';

        if ($caseInsensitive) {
            return stripos(strrev((string) $value), strrev($needle)) === 0;
        }

        return substr((string) $value, -strlen($needle)) === $needle;
    }

    /**
     * Validate field equals another field
     */
    protected function validate_equalsfield($value, $args = null): bool
    {
        // This would need access to full input data
        // Implementation requires modification to pass full input
        return false;
    }

    /**
     * Validate exact length
     */
    protected function validate_exactlen($value, $args = null): bool
    {
        if (empty($args)) {
            return false;
        }

        $length = (int) $args[0];
        $strLength = $this->_mbSupported ? mb_strlen((string) $value) : strlen((string) $value);
        
        return $strLength === $length;
    }

    /**
     * Validate float
     */
    protected function validate_float($value, $args = null): bool
    {
        return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
    }

    /**
     * Validate GUID v4
     */
    protected function validate_guidv4($value, $args = null): bool
    {
        return (bool) preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', (string) $value);
    }

    /**
     * Validate IBAN
     */
    protected function validate_iban($value, $args = null): bool
    {
        $iban = strtoupper(str_replace(' ', '', (string) $value));
        
        if (!preg_match('/^[A-Z]{2}[0-9]{2}[A-Z0-9]+$/', $iban)) {
            return false;
        }

        $country = substr($iban, 0, 2);
        $checkDigits = substr($iban, 2, 2);
        $account = substr($iban, 4);

        $numericIban = $account . $country . $checkDigits;
        $numericIban = str_split($numericIban);
        
        $newString = '';
        foreach ($numericIban as $char) {
            if (is_numeric($char)) {
                $newString .= $char;
            } else {
                $newString .= (ord($char) - 55);
            }
        }

        return bcmod($newString, '97') === '1';
    }

    /**
     * Validate value is in list (case insensitive)
     */
    protected function validate_inlistci($value, $args = null): bool
    {
        if (empty($args)) {
            return false;
        }

        return in_array(strtolower(trim((string) $value)), array_map('strtolower', array_map('trim', $args)), true);
    }

    /**
     * Validate value is in list (case sensitive)
     */
    protected function validate_inlistcs($value, $args = null): bool
    {
        if (empty($args)) {
            return false;
        }

        return in_array(trim((string) $value), array_map('trim', $args), true);
    }

    /**
     * Validate integer
     */
    protected function validate_integer($value, $args = null): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    /**
     * Validate IP address
     */
    protected function validate_ip($value, $args = null): bool
    {
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * Validate IPv4 address
     */
    protected function validate_ipv4($value, $args = null): bool
    {
        return filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false;
    }

    /**
     * Validate IPv6 address
     */
    protected function validate_ipv6($value, $args = null): bool
    {
        return filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false;
    }

    /**
     * Validate JSON string
     */
    protected function validate_jsonstring($value, $args = null): bool
    {
        if (!is_string($value)) {
            return false;
        }

        json_decode($value);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Validate maximum length
     */
    protected function validate_maxlen($value, $args = null): bool
    {
        if (empty($args)) {
            return false;
        }

        $maxLength = (int) $args[0];
        $strLength = $this->_mbSupported ? mb_strlen((string) $value) : strlen((string) $value);
        
        return $strLength <= $maxLength;
    }

    /**
     * Validate maximum numeric value
     */
    protected function validate_maxnumeric($value, $args = null): bool
    {
        if (empty($args) || !is_numeric($value)) {
            return false;
        }

        return (float) $value <= (float) $args[0];
    }

    /**
     * Validate minimum age
     */
    protected function validate_minage($value, $args = null): bool
    {
        if (empty($args)) {
            return false;
        }

        $minAge = (int) $args[0];
        $date = \DateTime::createFromFormat('Y-m-d', (string) $value);
        
        if (!$date) {
            return false;
        }

        $now = new \DateTime();
        $age = $now->diff($date)->y;
        
        return $age >= $minAge;
    }

    /**
     * Validate minimum length
     */
    protected function validate_minlen($value, $args = null): bool
    {
        if (empty($args)) {
            return false;
        }

        $minLength = (int) $args[0];
        $strLength = $this->_mbSupported ? mb_strlen((string) $value) : strlen((string) $value);
        
        return $strLength >= $minLength;
    }

    /**
     * Validate minimum numeric value
     */
    protected function validate_minnumeric($value, $args = null): bool
    {
        if (empty($args) || !is_numeric($value)) {
            return false;
        }

        return (float) $value >= (float) $args[0];
    }

    /**
     * Validate field is not empty
     */
    protected function validate_notempty($value, $args = null): bool
    {
        return !($value === null || $value === '');
    }

    /**
     * Validate value is not in list (case insensitive)
     */
    protected function validate_notinlistci($value, $args = null): bool
    {
        if (empty($args)) {
            return true;
        }

        return !in_array(strtolower(trim((string) $value)), array_map('strtolower', array_map('trim', $args)), true);
    }

    /**
     * Validate value is not in list (case sensitive)
     */
    protected function validate_notinlistcs($value, $args = null): bool
    {
        if (empty($args)) {
            return true;
        }

        return !in_array(trim((string) $value), array_map('trim', $args), true);
    }

    /**
     * Validate numeric value
     */
    protected function validate_numeric($value, $args = null): bool
    {
        return is_numeric($value);
    }

    /**
     * Always passes validation (placeholder)
     */
    protected function validate_pass($value = null, $args = null): bool
    {
        return true;
    }

    /**
     * Validate person name
     */
    protected function validate_personname($value, $args = null): bool
    {
        return (bool) preg_match('/^([a-zÀ-ÿ\s\'-])+$/iu', (string) $value);
    }

    /**
     * Validate phone number
     */
    protected function validate_phonenumber($value, $args = null): bool
    {
        return (bool) preg_match('/^(\d[\s-]?)?[\(\[\s-]{0,2}?\d{3}[\)\]\s-]{0,2}?\d{3}[\s-]?\d{4}$/i', (string) $value);
    }

    /**
     * Validate using custom regex
     */
    protected function validate_regex($value, $args = null): bool
    {
        if (empty($args)) {
            return false;
        }

        return (bool) preg_match($args[0], (string) $value);
    }

    /**
     * Validate required field
     */
    protected function validate_required($value, $args = null): bool
    {
        return !($value === null || $value === '');
    }

    /**
     * Validate required file upload
     */
    protected function validate_requiredfile($value, $args = null): bool
    {
        return is_array($value) && isset($value['error']) && $value['error'] !== UPLOAD_ERR_NO_FILE;
    }

    /**
     * Validate field starts with specific string
     */
    protected function validate_startswith($value, $args = null): bool
    {
        if (empty($args)) {
            return true;
        }

        $needle = $args[0];
        $caseInsensitive = isset($args[1]) && $args[1] === 'caseinsensitive';

        if ($caseInsensitive) {
            return stripos((string) $value, $needle) === 0;
        }

        return strpos((string) $value, $needle) === 0;
    }

    /**
     * Validate street address
     */
    protected function validate_streetaddress($value, $args = null): bool
    {
        $hasLetter = preg_match('/[a-zA-Z]/', (string) $value);
        $hasDigit = preg_match('/\d/', (string) $value);
        $hasSpace = preg_match('/\s/', (string) $value);

        return $hasLetter && $hasDigit && $hasSpace;
    }

    /**
     * Validate URL
     */
    protected function validate_url($value, $args = null): bool
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Validate URL exists (DNS check)
     */
    protected function validate_urlexists($value, $args = null): bool
    {
        $url = parse_url(strtolower((string) $value));

        if (isset($url['host'])) {
            $url = $url['host'];
        } else {
            return false;
        }

        if (function_exists('checkdnsrr')) {
            return checkdnsrr($url, 'A') || checkdnsrr($url, 'AAAA');
        }

        return gethostbyname($url) !== $url;
    }

    // ==================== SANITIZATION METHODS ====================

    /**
     * Sanitize to string
     */
    protected function sanitize_string($value, $args = null)
    {
        return filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }

    /**
     * Trim whitespace
     */
    protected function sanitize_trim($value, $args = null)
    {
        return is_scalar($value) ? trim((string) $value) : $value;
    }

    /**
     * Convert to lowercase
     */
    protected function sanitize_lowercase($value, $args = null)
    {
        return is_scalar($value) ? strtolower((string) $value) : $value;
    }

    /**
     * Convert to uppercase
     */
    protected function sanitize_uppercase($value, $args = null)
    {
        return is_scalar($value) ? strtoupper((string) $value) : $value;
    }

    /**
     * Sanitize email
     */
    protected function sanitize_email($value, $args = null)
    {
        return filter_var($value, FILTER_SANITIZE_EMAIL);
    }

    /**
     * Sanitize URL
     */
    protected function sanitize_url($value, $args = null)
    {
        return filter_var($value, FILTER_SANITIZE_URL);
    }

    /**
     * Sanitize to integer
     */
    protected function sanitize_integer($value, $args = null)
    {
        return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Sanitize to float
     */
    protected function sanitize_float($value, $args = null)
    {
        return filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }

    /**
     * Strip HTML tags
     */
    protected function sanitize_striptags($value, $args = null)
    {
        $allowedTags = $args[0] ?? '';
        return is_scalar($value) ? strip_tags((string) $value, $allowedTags) : $value;
    }

    /**
     * Escape HTML special characters
     */
    protected function sanitize_htmlspecialchars($value, $args = null)
    {
        return is_scalar($value) ? htmlspecialchars((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8') : $value;
    }
}