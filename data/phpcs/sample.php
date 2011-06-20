<?php

/**
 *  class A
 *
 * Missing @category tag in class comment (PEAR.Commenting.ClassComment.MissingTag)
 * Missing @package tag in class comment
 * Missing @author tag in class comment
 * Missing @category tag in class comment
 * Missing @license tag in class comment
 * Missing @link tag in class comment
 *
 */
class A {
    // Opening brace of a class must be on the line after the definition (PEAR.Classes.ClassDeclaration.OpenBraceNewLine)
    var $a =  1;
    // Expected 1 blank line before member var; 0 found (Squiz.WhiteSpace.MemberVarSpacing.After)
    // Expected 1 space after "="; 2 found (Squiz.WhiteSpace.OperatorSpacing.SpacingAfter)

    public $_public;
    // Public member variable "_public" must not be prefixed with an underscore (PEAR.NamingConventions.ValidVariableName.PublicUnderscore)
    // Public member variable "_public" must not contain a leading underscore (Zend.NamingConventions.ValidVariableName.PublicHasUnderscore)

    private $private;
    // Private member variable "private" must be prefixed with an underscore (PEAR.NamingConventions.ValidVariableName.PrivateNoUnderscore)
    // Private member variable "private" must contain a leading underscore (Zend.NamingConventions.ValidVariableName.PrivateNoUnderscore)

    // this is protected
    // You must use "/**" style comments for a function comment (PEAR.Commenting.FunctionComment.WrongStyle)
    protected $protected;
    // Protected member variable "protected" must contain a leading underscore (Zend.NamingConventions.ValidVariableName.PrivateNoUnderscore)

    protected function protectedMethod()
    {
    }
    // Missing function doc comment (PEAR.Commenting.FunctionComment.Missing)

    private function privateMethod()
    {
    }
    // Private method name "A::privateMethod" must be prefixed with an underscore (PEAR.NamingConventions.ValidFunctionName.PrivateNoUnderscore)

    /**
     * function name test
     *
     * @param int $dogs
     *
     */
    // Missing comment for param "$dogs" at position 1 (PEAR.Commenting.FunctionComment.MissingParamComment)
    // Doc comment for var $dogs does not match actual variable name $cats at position 1 (PEAR.Commenting.FunctionComment.ParamNameNoMatch)
    // Missing @return tag in function comment (PEAR.Commenting.FunctionComment.MissingReturn)
    // Method name "A::get_something" is not in camel caps format (PEAR.NamingConventions.ValidFunctionName.NotCamelCaps)
    // Opening brace should be on a new line (PEAR.Functions.FunctionDeclaration.BraceOnSameLine)
    // There must be exactly one blank line before the tags in function comment (PEAR.Commenting.FunctionComment.SpacingBeforeTags)
    // No scope modifier specified for function "get_something" (Squiz.Scope.MethodScope.Missing)
    function get_something($cats){
    }

    /**
     * @param int $hop
     * @param string  $step
     */
    public function funcParamInvalid($hop, $step, $jump)
    {
    }

    /**
     * This function should be valid
     *
     * @param int $hop
     * @param string $step
     * @param string $jump
     *
     * @return void
     */
    public function funcParamValid($hop, $step, $jump)
    {
    }

    /**
     * @return int
     */
    public function printSomething($val)
    {
    // Function return type "int" is invalid (Squiz.Commenting.FunctionComment.InvalidReturn)
    // Opening brace should be on the same line as the declaration (Generic.Functions.OpeningFunctionBraceKernighanRitchie.BraceOnNewLine)
    // Missing short description in function doc comment (Squiz.Commenting.FunctionComment.MissingShort)
        echo(1);
        // Echoed strings should not be bracketed (Squiz.Strings.EchoedStrings.HasBracket)
        echo (2);
        // Language constructs must be followed by a single space; expected "echo (" but found "echo(" (Squiz.WhiteSpace.LanguageConstructSpacing.Incorrect)
        return 1;
    }
    // Expected //end printSomething() (Squiz.Commenting.ClosingDeclarationComment.Missing)
    // Function return type is not void, but function has no return statement (Squiz.Commenting.FunctionComment.InvalidNoReturn)

    /**
     * @param int $zero comment
     * @param int $one comment
     */
    // Missing short description in function doc comment (Squiz.Commenting.FunctionComment.MissingShort)
    // Expected "integer"; found "int" for $zero at position 1 (Squiz.Commenting.FunctionComment.IncorrectParamVarName)
    // Param comment must start with a capital letter (Squiz.Commenting.FunctionComment.ParamCommentNotCapital)
    // Param comment must end with a full stop (Squiz.Commenting.FunctionComment.ParamCommentFullStop)
    // The comments for parameters $zero (1) and $one (2) do not align (Squiz.Commenting.FunctionComment.ParameterCommentsNotAligned)
    // Param comment must start with a capital letter (Squiz.Commenting.FunctionComment.ParamCommentNotCapital)
    // Param comment must end with a full stop (Squiz.Commenting.FunctionComment.ParamCommentFullStop)
    // Missing @return tag in function comment (Squiz.Commenting.FunctionComment.MissingReturn)
    public function greting($zero = 0, $one=1)
    // Expected 0 spaces between default value and equals sign for argument "$zero"; 1 found (Squiz.Functions.FunctionDeclarationArgumentSpacing.SpaceAfterDefault)
    {
        // = align
        $hello = 'hello';
        $goodbye  = 'good bye';
        // Equals sign not aligned with surrounding assignments; expected 1 space but found 2 spaces (Generic.Formatting.MultipleStatementAlignment.NotSame)
        $sayonara = "sayonara";
        // String "sayonara" does not require double quotes; use single quotes instead (Squiz.Strings.DoubleQuoteUsage.NotRequired)
        $say_sayonara = "say{$sayonara}";
        // Variable "say_sayonara" is not in valid camel caps format (Squiz.NamingConventions.ValidVariableName.NotCamelCaps)_
        // Variable "$sayonara" not allowed in double quoted string; use concatenation instead (Squiz.Strings.DoubleQuoteUsage.ContainsVar)
    }
    // Expected "integer"; found "int" for $zero at position 1 (Squiz.Commenting.FunctionComment.IncorrectParamVarName)

    /**
     * Say hello
     *
     * @param $hello
     * @param string $error error message
     * @param string
     * @return void
     */
    // Private method name "A::helloBye" must be prefixed with an underscore (PEAR.NamingConventions.ValidFunctionName.PrivateNoUnderscore)
    private function say_hello($hello, $good,$bye="bye")
    {
        function_has_underscore();
        $varName1 = 0;
        $var_name2 = 0;
        // Variable "var_name2" is not in valid camel caps format (Zend.NamingConventions.ValidVariableName.NotCamelCaps)
        $array = array(1 => 0);
        $this->callMultiLineValid(
        $hello, $a, $b,
        $c, $d);
        // Multi-line function call not indented correctly; expected 12 spaces but found 8 (PEAR.Functions.FunctionCallSignature.Indent)
        // Closing parenthesis of a multi-line function call must be on a line by itself (PEAR.Functions.FunctionCallSignature.CloseBracketLine)
        $this->callMultiLine($hello, $a, $b,
        $c, $d);
        $this->callMultiLine2($hello, $a, $b,
        $c, $d);
        return new ArrayObject();
        echo 'unreachable line';
        $alsoUnreachableVar = 0;
    }

    function noComment($important){
        // i don't use $important
    }

}

switch ($variable) {
    // use indent for case ?
    case 1:
        echo 1;
        break;
        // no default
}
// Line indented incorrectly; expected 0 spaces, found 4 (PEAR.WhiteSpace.ScopeIndent.Incorrect)
// All SWITCH statements must contain a DEFAULT case (Squiz.ControlStructures.SwitchDeclaration.MissingDefault)
switch ($variable) {
case 1:
    echo 1;
    break;
}
// CASE keyword must be indented 4 spaces from SWITCH keyword (Squiz.ControlStructures.SwitchDeclaration.CaseIndent)

try{ challenge();
} catch ($e) {
}
//Expected "try {\n...} catch (...) {\n"; found "try{ ...} catch (...) {\n" (Squiz.ControlStructures.ControlSignature)
//Empty CATCH statement detected (Generic.CodeAnalysis.EmptyStatement.NotAllowed)
//Empty CATCH statement must have a comment to explain why the exception is not handled (Squiz.Commenting.EmptyCatchComment.Missing)
//Line indented incorrectly; expected at least 4 spaces, found 0 (Generic.WhiteSpace.ScopeIndent.Incorrect)


// too short var name?
$i = 1;

// var name with number ?
$id5 =5;
//Expected 1 space after "="; 0 found (Squiz.WhiteSpace.OperatorSpacing.NoSpaceAfter)

$a = 0; if ($a==1) echo 'one';
// Operator == prohibited; use === instead (Squiz.Operators.ComparisonOperatorUsage.NotAllowed)
// Each PHP statement must be on a line by itself (Generic.Formatting.DisallowMultipleStatements.SameLine)
// Implicit true comparisons prohibited; use === TRUE instead (Squiz.Operators.ComparisonOperatorUsage.ImplicitTrue)
// Inline IF statements are not allowed (Squiz.PHP.DisallowInlineIf.Found)
// Inline shorthand IF statement requires brackets around comparison (Squiz.ControlStructures.InlineIfDeclaration.NoBrackets)

($truth) ? ($anotherTruth ? 'yes' : 'no') : 0;

$array = array(1,2 , 3, 4);
$assoc1 = array('key'=>'value');
// Expected 1 space between "'key'" and double arrow; 0 found (Squiz.Arrays.ArrayDeclaration.NoSpaceBeforeDoubleArrow)
// Expected 1 space between double arrow and "'value'"; 0 found (Squiz.Arrays.ArrayDeclaration.NoSpaceAfterDoubleArrow)
// Expected 1 space before "=>"; 0 found (Squiz.WhiteSpace.OperatorSpacing.NoSpaceBefore)
// Expected 1 space after "=>"; 0 found (Squiz.WhiteSpace.OperatorSpacing.NoSpaceAfter)

$validAssoc = array( 'a' => 'b' );

// zend coding standard valid array
$sampleArray = array(1, 2, 3, 'Zend', 'Studio',
                     $a, $b, $c,
                     56.44, $d, 500);
// The first value in a multi-value array must be on a new line (Squiz.Arrays.ArrayDeclaration.FirstValueNoNewline)
// Array value not aligned correctly; expected 17 spaces but found 1 (Squiz.Arrays.ArrayDeclaration.ValueNotAligned)

// zend coding standard valid array
$sampleArray = array(
    1, 2, 3, 'Zend', 'Studio',
    $a, $b, $c,
    56.44, $d, 500);
// Array value not aligned correctly; expected 17 spaces but found 1 (Squiz.Arrays.ArrayDeclaration.ValueNotAligned)

// squiz coding standard valid array
$sampleArray = array(
                1, 2, 3, 'Zend', 'Studio',
                $a, $b, $c,
                56.44, $d, 500,
);

$sampleArray = array('firstKey'  => 'firstValue',
'secondKey' => 'secondValue');

if ($condition) {
    //File is being conditionally included; use "include" instead (PEAR.Files.IncludingFile.UseInclude)
    require 'a';
}

for ($i=0;$i<100;$j++)
{
}
// Expected "for (...) {\n"; found "for (...)\n{\n" (Squiz.ControlStructures.ControlSignature)
// Expected 1 space after first semicolon of FOR loop; 0 found (Squiz.ControlStructures.ForLoopDeclaration.NoSpaceAfterFirst)
// Expected 1 space after second semicolon of FOR loop; 0 found (Squiz.ControlStructures.ForLoopDeclaration.NoSpaceAfterSecond)
// Expected 1 space before "="; 0 found (Squiz.WhiteSpace.OperatorSpacing.NoSpaceBefore)
// Expected 1 space after "="; 0 found (Squiz.WhiteSpace.OperatorSpacing.NoSpaceAfter)

foreach ( $cats as $cat )  {
}
// Empty FOREACH statement detected (Squiz.CodeAnalysis.EmptyStatement.NotAllowed)

$casted = (array)$a;
// A cast statement must be followed by a single space (Generic.Formatting.SpaceAfterCast.NoSpace)
$casted = (array) $a;
//  A cast statement must not be followed by a space (Generic.Formatting.NoSpaceAfterCast.SpaceFound)

$a = true;
// TRUE, FALSE and NULL must be lowercase; expected "true" but found "TRUE" (Generic.PHP.LowerCaseConstant.Found)
$b = TRUE;
// TRUE, FALSE and NULL must be uppercase; expected "TRUE" but found "true" (Squiz.NamingConventions.ConstantCase.Found)

 // Missing file doc comment (PEAR.Commenting.FileComment.Missing)
 # this is perl style comment
 // Perl-style comments are not allowed; use "// Comment" instead (Squiz.Commenting.InlineComment.WrongStyle)

// end of line (CRLF)
 // End of line character is invalid; expected "\n" but found "\r\n" (Generic.Files.LineEndings.InvalidEOLChar)