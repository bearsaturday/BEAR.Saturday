<?php

function smarty_compiler_emoji($tagArg, &$smarty)
{
	$emoji = BEAR::dependency('BEAR_Emoji')->getAgentEmoji($tagArg);
	// SBの絵文字のエラーを避けるためecho文を使わない
    return '?>' . "{$emoji}" . '<?php ';
//    return 'echo "' . "{$emoji}" . '";';
}