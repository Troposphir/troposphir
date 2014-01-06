<pre><?php
/** get leading, trailing, and embedded separator tokens that were 'skipped'
if for some ungodly reason you are using php to implement a simple parser that 
needs to detect nested clauses as it builds a parse tree */

$str = "((is.lotd:true) OR (xp.level:true OR ct:[100 TO 1000] ) AND (draft:false AND deleted:false AND version:2 AND xgms:1))";
SQLLBuilder::Convert($str);
/**
 *
 * This is a static class used to convert Lucene queries to SQL compatible queries.
 *
 */
 

class TokenDefinition
{
	var $symbol = null;
	var $tokenType_ = null;
	var $symbolLen = 0;
		
	public function __construct($symbol, $tokenType) {
		$this->symbol = $symbol;
		$this->tokenType_ = $tokenType;
		$this->symbolLen = strlen($symbol);
	}
	
	public function Match($str, $pos) {
			$matches = preg_match($this->symbol, $str, $matches, PREG_OFFSET_CAPTURE, $pos);
			print_r($matches);
			return $matches;
	}
}
 
class SQLLBuilder 
{
		/**
		 * Convert Lucene query string to SQL query string.
		 *
		 * @param  [string $query] Lucene query.
		 * @return [string] SQL formatted query.
		 */
		public static function Convert($query) 
		{
			//Variable initializations
			$defs = null;
			$position = 0;
			$query_len = 0;
			$query_len = strlen($query);
			if ($defs == null) {
				$defs[] = new TokenDefinition("(", 'LEFT PARANTHESES');
				$defs[] = new TokenDefinition(")", 'RIGHT PARANTHESES');
				$defs[] = new TokenDefinition("AND", 'SQL SYMBOL "AND"');
				$defs[] = new TokenDefinition(" ", 'SPACE');
				$defs[] = new TokenDefinition(":", 'COLON');
			}
			
			//Tokenization
			$tokenStack = null;
			$tokenize = true;
			while ($position < $query_len)
			{
				$offset_pos = null;
				$len = 0;
				foreach ($defs as $i => $tokenDef)
				{
					$new_pos = strpos($query, $tokenDef->symbol, $position);
					if ($new_pos === FALSE) {
						//Current token definition could not be matched to the rest of the string.
						//Remove it from iteration.
						unset($defs[$i]);
						continue;
					}
				
					if ($offset_pos === null || $new_pos < $offset_pos) {
						$offset_pos = $new_pos;
						$len = $tokenDef->symbolLen;
					}					
				}
				if ($offset_pos == $position) $offset_pos += $len;
				
				echo substr($query, $position, ($offset_pos - $position)) . "</br>";
				$position = $offset_pos;
			}
			return "h";
		}
}

?>