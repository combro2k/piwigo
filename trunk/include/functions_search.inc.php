<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2014 Piwigo Team                  http://piwigo.org |
// | Copyright(C) 2003-2008 PhpWebGallery Team    http://phpwebgallery.net |
// | Copyright(C) 2002-2003 Pierrick LE GALL   http://le-gall.net/pierrick |
// +-----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify  |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation                                          |
// |                                                                       |
// | This program is distributed in the hope that it will be useful, but   |
// | WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU      |
// | General Public License for more details.                              |
// |                                                                       |
// | You should have received a copy of the GNU General Public License     |
// | along with this program; if not, write to the Free Software           |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, |
// | USA.                                                                  |
// +-----------------------------------------------------------------------+

/**
 * @package functions\search
 */


/**
 * Returns search rules stored into a serialized array in "search"
 * table. Each search rules set is numericaly identified.
 *
 * @param int $search_id
 * @return array
 */
function get_search_array($search_id)
{
  if (!is_numeric($search_id))
  {
    die('Search id must be an integer');
  }

  $query = '
SELECT rules
  FROM '.SEARCH_TABLE.'
  WHERE id = '.$search_id.'
;';
  list($serialized_rules) = pwg_db_fetch_row(pwg_query($query));

  return unserialize($serialized_rules);
}

/**
 * Returns the SQL clause for a search.
 * Transforms the array returned by get_search_array() into SQL sub-query.
 *
 * @param array $search
 * @return string
 */
function get_sql_search_clause($search)
{
  // SQL where clauses are stored in $clauses array during query
  // construction
  $clauses = array();

  foreach (array('file','name','comment','author') as $textfield)
  {
    if (isset($search['fields'][$textfield]))
    {
      $local_clauses = array();
      foreach ($search['fields'][$textfield]['words'] as $word)
      {
        $local_clauses[] = $textfield." LIKE '%".$word."%'";
      }

      // adds brackets around where clauses
      $local_clauses = prepend_append_array_items($local_clauses, '(', ')');

      $clauses[] = implode(
        ' '.$search['fields'][$textfield]['mode'].' ',
        $local_clauses
        );
    }
  }

  if (isset($search['fields']['allwords']))
  {
    $fields = array('file', 'name', 'comment', 'author');
    // in the OR mode, request bust be :
    // ((field1 LIKE '%word1%' OR field2 LIKE '%word1%')
    // OR (field1 LIKE '%word2%' OR field2 LIKE '%word2%'))
    //
    // in the AND mode :
    // ((field1 LIKE '%word1%' OR field2 LIKE '%word1%')
    // AND (field1 LIKE '%word2%' OR field2 LIKE '%word2%'))
    $word_clauses = array();
    foreach ($search['fields']['allwords']['words'] as $word)
    {
      $field_clauses = array();
      foreach ($fields as $field)
      {
        $field_clauses[] = $field." LIKE '%".$word."%'";
      }
      // adds brackets around where clauses
      $word_clauses[] = implode(
        "\n          OR ",
        $field_clauses
        );
    }

    array_walk(
      $word_clauses,
      create_function('&$s','$s="(".$s.")";')
      );

    // make sure the "mode" is either OR or AND
    if ($search['fields']['allwords']['mode'] != 'AND' and $search['fields']['allwords']['mode'] != 'OR')
    {
      $search['fields']['allwords']['mode'] = 'AND';
    }

    $clauses[] = "\n         ".
      implode(
        "\n         ". $search['fields']['allwords']['mode']. "\n         ",
        $word_clauses
        );
  }

  foreach (array('date_available', 'date_creation') as $datefield)
  {
    if (isset($search['fields'][$datefield]))
    {
      $clauses[] = $datefield." = '".$search['fields'][$datefield]['date']."'";
    }

    foreach (array('after','before') as $suffix)
    {
      $key = $datefield.'-'.$suffix;

      if (isset($search['fields'][$key]))
      {
        $clauses[] = $datefield.
          ($suffix == 'after'             ? ' >' : ' <').
          ($search['fields'][$key]['inc'] ? '='  : '').
          " '".$search['fields'][$key]['date']."'";
      }
    }
  }

  if (isset($search['fields']['cat']))
  {
    if ($search['fields']['cat']['sub_inc'])
    {
      // searching all the categories id of sub-categories
      $cat_ids = get_subcat_ids($search['fields']['cat']['words']);
    }
    else
    {
      $cat_ids = $search['fields']['cat']['words'];
    }

    $local_clause = 'category_id IN ('.implode(',', $cat_ids).')';
    $clauses[] = $local_clause;
  }

  // adds brackets around where clauses
  $clauses = prepend_append_array_items($clauses, '(', ')');

  $where_separator =
    implode(
      "\n    ".$search['mode'].' ',
      $clauses
      );

  $search_clause = $where_separator;

  return $search_clause;
}

/**
 * Returns the list of items corresponding to the advanced search array.
 *
 * @param array $search
 * @param string $images_where optional additional restriction on images table
 * @return array
 */
function get_regular_search_results($search, $images_where='')
{
  global $conf;
  $forbidden = get_sql_condition_FandF(
        array
          (
            'forbidden_categories' => 'category_id',
            'visible_categories' => 'category_id',
            'visible_images' => 'id'
          ),
        "\n  AND"
    );

  $items = array();
  $tag_items = array();

  if (isset($search['fields']['tags']))
  {
    $tag_items = get_image_ids_for_tags(
      $search['fields']['tags']['words'],
      $search['fields']['tags']['mode']
      );
  }

  $search_clause = get_sql_search_clause($search);

  if (!empty($search_clause))
  {
    $query = '
SELECT DISTINCT(id)
  FROM '.IMAGES_TABLE.' i
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON id = ic.image_id
  WHERE '.$search_clause;
    if (!empty($images_where))
    {
      $query .= "\n  AND ".$images_where;
    }
    $query .= $forbidden.'
  '.$conf['order_by'];
    $items = array_from_query($query, 'id');
  }

  if ( !empty($tag_items) )
  {
    switch ($search['mode'])
    {
      case 'AND':
        if (empty($search_clause))
        {
          $items = $tag_items;
        }
        else
        {
          $items = array_values( array_intersect($items, $tag_items) );
        }
        break;
      case 'OR':
        $before_count = count($items);
        $items = array_unique(
          array_merge(
            $items,
            $tag_items
            )
          );
        break;
    }
  }

  return $items;
}

/**
 * Finds if a char is a letter, a figure or any char of the extended ASCII table (>127).
 *
 * @param char $ch
 * @return bool
 */
function is_word_char($ch)
{
  return ($ch>='0' && $ch<='9') || ($ch>='a' && $ch<='z') || ($ch>='A' && $ch<='Z') || ord($ch)>127;
}

/**
 * Finds if a char is a special token for word start: [{<=*+
 *
 * @param char $ch
 * @return bool
 */
function is_odd_wbreak_begin($ch)
{
  return strpos('[{<=*+', $ch)===false ? false:true;
}

/**
 * Finds if a char is a special token for word end: ]}>=*+
 *
 * @param char $ch
 * @return bool
 */
function is_odd_wbreak_end($ch)
{
  return strpos(']}>=*+', $ch)===false ? false:true;
}


define('QST_QUOTED',         0x01);
define('QST_NOT',            0x02);
define('QST_OR',             0x04);
define('QST_WILDCARD_BEGIN', 0x08);
define('QST_WILDCARD_END',   0x10);
define('QST_WILDCARD', QST_WILDCARD_BEGIN|QST_WILDCARD_END);

/**
 * Analyzes and splits the quick/query search query $q into tokens.
 * q='john bill' => 2 tokens 'john' 'bill'
 * Special characters for MySql full text search (+,<,>,~) appear in the token modifiers.
 * The query can contain a phrase: 'Pierre "New York"' will return 'pierre' qnd 'new york'.
 *
 * @param string $q
 */

/** Represents a single word or quoted phrase to be searched.*/
class QSingleToken
{
  var $is_single = true;
  var $token; /* the actual word/phrase string*/
  var $idx;

  function __construct($token)
  {
    $this->token = $token;
  }
}

/** Represents an expression of several words or sub expressions to be searched.*/
class QMultiToken
{
  var $is_single = false;
  var $tokens = array(); // the actual array of QSingleToken or QMultiToken
  var $token_modifiers = array(); // modifiers (OR,NOT,...) for every token

  function __toString()
  {
    $s = '';
    for ($i=0; $i<count($this->tokens); $i++)
    {
      $modifier = $this->token_modifiers[$i];
      if ($i)
        $s .= ' ';
      if ($modifier & QST_OR)
        $s .= 'OR ';
      if ($modifier & QST_NOT)
        $s .= 'NOT ';
      if ($modifier & QST_WILDCARD_BEGIN)
        $s .= '*';
      if ($modifier & QST_QUOTED)
        $s .= '"';
      if (! ($this->tokens[$i]->is_single) )
      {
        $s .= '(';
        $s .= $this->tokens[$i];
        $s .= ')';
      }
      else
      {
        $s .= $this->tokens[$i]->token;
      }
      if ($modifier & QST_QUOTED)
        $s .= '"';
      if ($modifier & QST_WILDCARD_END)
        $s .= '*';

    }
    return $s;
  }

  private function push(&$token, &$modifier)
  {
    $this->tokens[] = new QSingleToken($token);
    $this->token_modifiers[] = $modifier;
    $token = "";
    $modifier = 0;
  }

  /**
  * Parses the input query string by tokenizing the input, generating the modifiers (and/or/not/quotation/wildcards...).
  * Recursivity occurs when parsing ()
  * @param string $q the actual query to be parsed
  * @param int $qi the character index in $q where to start parsing
  * @param int $level the depth from root in the tree (number of opened and unclosed opening brackets)
  */
  protected function parse_expression($q, &$qi, $level)
  {
    $crt_token = "";
    $crt_modifier = 0;

    for ($stop=false; !$stop && $qi<strlen($q); $qi++)
    {
      $ch = $q[$qi];
      if ( ($crt_modifier&QST_QUOTED)==0)
      {
        switch ($ch)
        {
          case '(':
            if (strlen($crt_token))
              $this->push($crt_token, $crt_modifier);
            $sub = new QMultiToken;
            $qi++;
            $sub->parse_expression($q, $qi, $level+1);
            $this->tokens[] = $sub;
            $this->token_modifiers[] = $crt_modifier;
            $crt_modifier = 0;
            break;
          case ')':
            if ($level>0)
              $stop = true;
            break;
          case '"':
            if (strlen($crt_token))
              $this->push($crt_token, $crt_modifier);
            $crt_modifier |= QST_QUOTED;
            break;
          case '-':
            if (strlen($crt_token))
              $crt_token .= $ch;
            else
              $crt_modifier |= QST_NOT;
            break;
          case '*':
            if (strlen($crt_token))
              $crt_token .= $ch; // wildcard end later
            else
              $crt_modifier |= QST_WILDCARD_BEGIN;
            break;
          default:
            if (preg_match('/[\s,.;!\?]+/', $ch))
            { // white space
              if (strlen($crt_token))
                $this->push($crt_token, $crt_modifier);
              $crt_modifier = 0;
            }
            else
              $crt_token .= $ch;
            break;
        }
      }
      else
      {// quoted
        if ($ch=='"')
        {
          if ($qi+1 < strlen($q) && $q[$qi+1]=='*')
          {
            $crt_modifier |= QST_WILDCARD_END;
            $ai++;
          }
          $this->push($crt_token, $crt_modifier);
        }
        else
          $crt_token .= $ch;
      }
    }

    if (strlen($crt_token))
      $this->push($crt_token, $crt_modifier);

    for ($i=0; $i<count($this->tokens); $i++)
    {
      $token = $this->tokens[$i];
      $remove = false;
      if ($token->is_single)
      {
        if ( ($this->token_modifiers[$i]&QST_QUOTED)==0 )
        {
          if ('not' == strtolower($token->token))
          {
            if ($i+1 < count($this->tokens))
              $this->token_modifiers[$i+1] |= QST_NOT;
            $token->token = "";
          }
          if ('or' == strtolower($token->token))
          {
            if ($i+1 < count($this->tokens))
              $this->token_modifiers[$i+1] |= QST_OR;
            $token->token = "";
          }
          if ('and' == strtolower($token->token))
          {
            $token->token = "";
          }
          if ( substr($token->token, -1)=='*' )
          {
            $token->token = rtrim($token->token, '*');
            $this->token_modifiers[$i] |= QST_WILDCARD_END;
          }
        }
        if (!strlen($token->token))
          $remove = true;
      }
      else
      {
        if (!count($token->tokens))
          $remove = true;
      }
      if ($remove)
      {
        array_splice($this->tokens, $i, 1);
        array_splice($this->token_modifiers, $i, 1);
        $i--;
      }
    }
  }

  private static function priority($modifier)
  {
    return $modifier & QST_OR ? 0 :1;
  }

  /* because evaluations occur left to right, we ensure that 'a OR b c d' is interpreted as 'a OR (b c d)'*/
  protected function check_operator_priority()
  {
    for ($i=0; $i<count($this->tokens); $i++)
    {
      if (!$this->tokens[$i]->is_single)
        $this->tokens[$i]->check_operator_priority();
      if ($i==1)
        $crt_prio = self::priority($this->token_modifiers[$i]);
      if ($i<=1)
        continue;
      $prio = self::priority($this->token_modifiers[$i]);
      if ($prio > $crt_prio)
      {// e.g. 'a OR b c d' i=2, operator(c)=AND -> prio(AND) > prio(OR) = operator(b)
        $term_count = 2; // at least b and c to be regrouped
        for ($j=$i+1; $j<count($this->tokens); $j++)
        {
          if (self::priority($this->token_modifiers[$j]) >= $prio)
            $term_count++; // also take d
          else
            break;
        }

        $i--; // move pointer to b
        // crate sub expression (b c d)
        $sub = new QMultiToken;
        $sub->tokens = array_splice($this->tokens, $i, $term_count);
        $sub->token_modifiers = array_splice($this->token_modifiers, $i, $term_count);

        // rewrite ourseleves as a (b c d)
        array_splice($this->tokens, $i, 0, array($sub));
        array_splice($this->token_modifiers, $i, 0, array($sub->token_modifiers[0]&QST_OR));
        $sub->token_modifiers[0] &= ~QST_OR;

        $sub->check_operator_priority();
      }
      else
        $crt_prio = $prio;
    }
  }
}

class QExpression extends QMultiToken
{
  var $stokens = array();
  var $stoken_modifiers = array();

  function __construct($q)
  {
    $i = 0;
    $this->parse_expression($q, $i, 0);
    //manipulate the tree so that 'a OR b c' is the same as 'b c OR a'
    $this->check_operator_priority();
    $this->build_single_tokens($this, 0);
  }

  private function build_single_tokens(QMultiToken $expr, $this_is_not)
  {
    for ($i=0; $i<count($expr->tokens); $i++)
    {
      $token = $expr->tokens[$i];
      $crt_is_not = ($expr->token_modifiers[$i] ^ $this_is_not) & QST_NOT; // no negation OR double negation -> no negation;

      if ($token->is_single)
      {
        $token->idx = count($this->stokens);
        $this->stokens[] = $token->token;

        $modifier = $expr->token_modifiers[$i];
        if ($crt_is_not)
          $modifier |= QST_NOT;
        else
          $modifier &= ~QST_NOT;
        $this->stoken_modifiers[] = $modifier;
      }
      else
        $this->build_single_tokens($token, $crt_is_not);
    }
  }
}

/**
  Structure of results being filled from different tables
*/
class QResults
{
  var $all_tags;
  var $tag_ids;
  var $tag_iids;
  var $images_iids;
  var $iids;

  var $variants;
}

function qsearch_get_images(QExpression $expr, QResults $qsr)
{
  //@TODO: inflections for english / french
  $qsr->images_iids = array_fill(0, count($expr->tokens), array());

  $inflector = null;
  $lang_code = substr(get_default_language(),0,2);
  include_once(PHPWG_ROOT_PATH.'include/inflectors/'.$lang_code.'.php');
  $class_name = 'Inflector_'.$lang_code;
  if (class_exists($class_name))
  {
    $inflector = new $class_name;
  }

  $query_base = 'SELECT id from '.IMAGES_TABLE.' i WHERE ';
  for ($i=0; $i<count($expr->stokens); $i++)
  {
    $token = $expr->stokens[$i];
    $clauses = array();

    $like = addslashes($token);
    $like = str_replace( array('%','_'), array('\\%','\\_'), $like); // escape LIKE specials %_
    $clauses[] = 'CONVERT(file, CHAR) LIKE \'%'.$like.'%\'';

    if ($inflector!=null && strlen($token)>2
      && ($expr->stoken_modifiers[$i] & (QST_QUOTED|QST_WILDCARD))==0
      && strcspn($token, '\'0123456789') == strlen($token)
      )
    {
      $variants = array_unique( array_diff( $inflector->get_variants($token), array($token) ) );
      $qsr->variants[$token] = $variants;
    }
    else
    {
      $variants = array();
    }

    if (strlen($token)>3) // default minimum full text index
    {
      $ft = $token;
      if ($expr->stoken_modifiers[$i] & QST_QUOTED)
        $ft = '"'.$ft.'"';
      if ($expr->stoken_modifiers[$i] & QST_WILDCARD_END)
        $ft .= '*';
      foreach ($variants as $variant)
      {
        $ft.=' '.$variant;
      }
      $clauses[] = 'MATCH(i.name, i.comment) AGAINST( \''.addslashes($ft).'\' IN BOOLEAN MODE)';
    }
    else
    {
      foreach( array('i.name', 'i.comment') as $field)
      {
        /*$clauses[] = $field.' LIKE \''.$like.' %\'';
        $clauses[] = $field.' LIKE \'% '.$like.'\'';
        $clauses[] = $field.' LIKE \'% '.$like.' %\'';*/
        $clauses[] = $field.' REGEXP \'[[:<:]]'.addslashes(preg_quote($token)).'[[:>:]]\'';
      }
    }
    $query = $query_base.'('.implode(' OR ', $clauses).')';
    $qsr->images_iids[$i] = query2array($query,null,'id');
  }
}

function qsearch_get_tags(QExpression $expr, QResults $qsr)
{
  $tokens = $expr->stokens;
  $token_modifiers = $expr->stoken_modifiers;

  $token_tag_ids = array_fill(0, count($tokens), array() );
  $all_tags = array();

  $token_tag_scores = $token_tag_ids;
  $transliterated_tokens = array();
  foreach ($tokens as $token)
  {
    $transliterated_tokens[] = transliterate($token);
  }

  $query = '
SELECT t.*, COUNT(image_id) AS counter
  FROM '.TAGS_TABLE.' t
    INNER JOIN '.IMAGE_TAG_TABLE.' ON id=tag_id
  GROUP BY id';
  $result = pwg_query($query);
  while ($tag = pwg_db_fetch_assoc($result))
  {
    $transliterated_tag = transliterate($tag['name']);

    // find how this tag matches query tokens
    for ($i=0; $i<count($tokens); $i++)
    {
      $transliterated_token = $transliterated_tokens[$i];

      $match = false;
      $pos = 0;
      while ( ($pos = strpos($transliterated_tag, $transliterated_token, $pos)) !== false)
      {
        if ( ($token_modifiers[$i]&QST_WILDCARD)==QST_WILDCARD )
        {// wildcard in this token
          $match = 1;
          break;
        }
        $token_len = strlen($transliterated_token);

        // search begin of word
        $wbegin_len=0; $wbegin_char=' ';
        while ($pos-$wbegin_len > 0)
        {
          if (! is_word_char($transliterated_tag[$pos-$wbegin_len-1]) )
          {
            $wbegin_char = $transliterated_tag[$pos-$wbegin_len-1];
            break;
          }
          $wbegin_len++;
        }

        // search end of word
        $wend_len=0; $wend_char=' ';
        while ($pos+$token_len+$wend_len < strlen($transliterated_tag))
        {
          if (! is_word_char($transliterated_tag[$pos+$token_len+$wend_len]) )
          {
            $wend_char = $transliterated_tag[$pos+$token_len+$wend_len];
            break;
          }
          $wend_len++;
        }

        $this_score = 0;
        if ( ($token_modifiers[$i]&QST_WILDCARD)==0 )
        {// no wildcard begin or end
          if ($token_len <= 2)
          {// search for 1 or 2 characters must match exactly to avoid retrieving too much data
            if ($wbegin_len==0 && $wend_len==0 && !is_odd_wbreak_begin($wbegin_char) && !is_odd_wbreak_end($wend_char) )
              $this_score = 1;
          }
          elseif ($token_len == 3)
          {
            if ($wbegin_len==0)
              $this_score = $token_len / ($token_len + $wend_len);
          }
          else
          {
            $this_score = $token_len / ($token_len + 1.1 * $wbegin_len + 0.9 * $wend_len);
          }
        }

        if ($this_score>0)
          $match = max($match, $this_score );
        $pos++;
      }

      if ($match)
      {
        $tag_id = (int)$tag['id'];
        $all_tags[$tag_id] = $tag;
        $token_tag_ids[$i][] = $tag_id;
        $token_tag_scores[$i][] = $match;
      }
    }
  }

  // process tags
  $not_tag_ids = array();
  for ($i=0; $i<count($tokens); $i++)
  {
    array_multisort($token_tag_scores[$i], SORT_DESC|SORT_NUMERIC, $token_tag_ids[$i]);
    $is_not = $token_modifiers[$i]&QST_NOT;
    $counter = 0;

    for ($j=0; $j<count($token_tag_scores[$i]); $j++)
    {
      if ($is_not)
      {
        if ($token_tag_scores[$i][$j] < 0.8 ||
              ($j>0 && $token_tag_scores[$i][$j] < $token_tag_scores[$i][0]) )
        {
          array_splice($token_tag_scores[$i], $j);
          array_splice($token_tag_ids[$i], $j);
        }
      }
      else
      {
        $tag_id = $token_tag_ids[$i][$j];
        $counter += $all_tags[$tag_id]['counter'];
        if ( $j>0 && (
          ($counter > 100 && $token_tag_scores[$i][0] > $token_tag_scores[$i][$j]) // "many" images in previous tags and starting from this tag is less relevant
          || ($token_tag_scores[$i][0]==1 && $token_tag_scores[$i][$j]<0.8)
          || ($token_tag_scores[$i][0]>0.8 && $token_tag_scores[$i][$j]<0.5)
          ))
        {// we remove this tag from the results, but we still leave it in all_tags list so that if we are wrong, the user chooses it
          array_splice($token_tag_ids[$i], $j);
          array_splice($token_tag_scores[$i], $j);
          break;
        }
      }
    }

    if ($is_not)
    {
      $not_tag_ids = array_merge($not_tag_ids, $token_tag_ids[$i]);
    }
  }

  $all_tags = array_diff_key($all_tags, array_flip($not_tag_ids));
  usort($all_tags, 'tag_alpha_compare');
  foreach ( $all_tags as &$tag )
  {
    $tag['name'] = trigger_event('render_tag_name', $tag['name'], $tag);
  }
  $qsr->all_tags = $all_tags;

  $qsr->tag_ids = $token_tag_ids;
  $qsr->tag_iids = array_fill(0, count($tokens), array() );

  for ($i=0; $i<count($tokens); $i++)
  {
    $tag_ids = $token_tag_ids[$i];

    if (!empty($tag_ids))
    {
      $query = '
SELECT image_id FROM '.IMAGE_TAG_TABLE.'
  WHERE tag_id IN ('.implode(',',$tag_ids).')
  GROUP BY image_id';
      $qsr->tag_iids[$i] = query2array($query, null, 'image_id');
    }
  }
}


function qsearch_eval(QMultiToken $expr, QResults $qsr, &$qualifies, &$ignored_terms)
{
  $qualifies = false; // until we find at least one positive term
  $ignored_terms = array();

  $ids = $not_ids = array();

  for ($i=0; $i<count($expr->tokens); $i++)
  {
    $crt = $expr->tokens[$i];
    if ($crt->is_single)
    {
      $crt_ids = $qsr->iids[$crt->idx] = array_unique( array_merge($qsr->images_iids[$crt->idx], $qsr->tag_iids[$crt->idx]) );
      $crt_qualifies = count($crt_ids)>0 || count($qsr->tag_ids[$crt->idx])>0;
      $crt_ignored_terms = $crt_qualifies ? array() : array($crt->token);
    }
    else
      $crt_ids = qsearch_eval($crt, $qsr, $crt_qualifies, $crt_ignored_terms);

    $modifier = $expr->token_modifiers[$i];
    if ($modifier & QST_NOT)
      $not_ids = array_unique( array_merge($not_ids, $crt_ids));
    else
    {
      $ignored_terms = array_merge($ignored_terms, $crt_ignored_terms);
      if ($modifier & QST_OR)
      {
        $ids = array_unique( array_merge($ids, $crt_ids) );
        $qualifies |= $crt_qualifies;
      }
      elseif ($crt_qualifies)
      {
        if ($qualifies)
          $ids = array_intersect($ids, $crt_ids);
        else
          $ids = $crt_ids;
        $qualifies = true;
      }
    }
  }

  if (count($not_ids))
    $ids = array_diff($ids, $not_ids);
  return $ids;
}

/**
 * Returns the search results corresponding to a quick/query search.
 * A quick/query search returns many items (search is not strict), but results
 * are sorted by relevance unless $super_order_by is true. Returns:
 *  array (
 *    'items' => array of matching images
 *    'qs'    => array(
 *      'unmatched_terms' => array of terms from the input string that were not matched
 *      'matching_tags' => array of matching tags
 *      'matching_cats' => array of matching categories
 *      'matching_cats_no_images' =>array(99) - matching categories without images
 *      )
 *    )
 *
 * @param string $q
 * @param bool $super_order_by
 * @param string $images_where optional additional restriction on images table
 * @return array
 */
function get_quick_search_results($q, $super_order_by, $images_where='')
{
  global $conf;
  //@TODO: maybe cache for 10 minutes the result set to avoid many expensive sql calls when navigating the pictures
  $q = trim(stripslashes($q));
  $search_results =
    array(
      'items' => array(),
      'qs' => array('q'=>$q),
    );

  $expression = new QExpression($q);
//var_export($expression);

  $qsr = new QResults;
  qsearch_get_tags($expression, $qsr);
  qsearch_get_images($expression, $qsr);
//var_export($qsr->all_tags);

  $ids = qsearch_eval($expression, $qsr, $tmp, $search_results['qs']['unmatched_terms']);

  $debug[] = "<!--\nparsed: ".$expression;
  $debug[] = count($expression->stokens).' tokens';
  for ($i=0; $i<count($expression->stokens); $i++)
  {
    $debug[] = $expression->stokens[$i].': '.count($qsr->tag_ids[$i]).' tags, '.count($qsr->tag_iids[$i]).' tiids, '.count($qsr->images_iids[$i]).' iiids, '.count($qsr->iids[$i]).' iids'
      .( !empty($qsr->variants[$expression->stokens[$i]]) ? ' variants: '.implode(', ',$qsr->variants[$expression->stokens[$i]]): '');
  }
  $debug[] = 'before perms '.count($ids);

  $search_results['qs']['matching_tags'] = $qsr->all_tags;
  global $template;

  if (empty($ids))
  {
    $debug[] = '-->';
    $template->append('footer_elements', implode("\n", $debug) );
    return $search_results;
  }

  $where_clauses = array();
  $where_clauses[]='i.id IN ('. implode(',', $ids) . ')';
  if (!empty($images_where))
  {
    $where_clauses[]='('.$images_where.')';
  }
  $where_clauses[] = get_sql_condition_FandF(
      array
        (
          'forbidden_categories' => 'category_id',
          'visible_categories' => 'category_id',
          'visible_images' => 'i.id'
        ),
      null,true
    );

  $query = '
SELECT DISTINCT(id)
  FROM '.IMAGES_TABLE.' i
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON id = ic.image_id
  WHERE '.implode("\n AND ", $where_clauses)."\n".
  $conf['order_by'];

  $ids = query2array($query, null, 'id');

  $debug[] = count($ids).' final photo count -->';
  $template->append('footer_elements', implode("\n", $debug) );

  $search_results['items'] = $ids;
  return $search_results;
}

/**
 * Returns an array of 'items' corresponding to the search id.
 * It can be either a quick search or a regular search.
 *
 * @param int $search_id
 * @param bool $super_order_by
 * @param string $images_where optional aditional restriction on images table
 * @return array
 */
function get_search_results($search_id, $super_order_by, $images_where='')
{
  $search = get_search_array($search_id);
  if ( !isset($search['q']) )
  {
    $result['items'] = get_regular_search_results($search, $images_where);
    return $result;
  }
  else
  {
    return get_quick_search_results($search['q'], $super_order_by, $images_where);
  }
}

?>