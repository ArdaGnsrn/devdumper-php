<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ArdaGnsrn\DevDumper\Dumper;

use Symfony\Component\VarDumper\Cloner\Cursor;
use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Component\VarDumper\Dumper\AbstractDumper;
use Symfony\Component\VarDumper\Dumper\CliDumper;

/**
 * HtmlDumper dumps variables as HTML.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class CustomDumper extends CliDumper
{
    public static $defaultOutput = 'php://output';

    protected $dumpHeader;

    protected $dumpPrefix = '<pre class=php-dump id=%s data-indent-pad="%s">';
    protected $dumpSuffix = '</pre>';
    protected $dumpId = 'php-dump';

    protected $dumpStyle = false;
    protected $colors = true;
    protected $headerIsDumped = false;
    protected $lastDepth = -1;
    protected $styles = array(
        'default' => 'background-color:#18171B; color:#FF8400; line-height:1.2em; font:12px Menlo, Monaco, Consolas, monospace; word-wrap: break-word; white-space: pre-wrap; position:relative; z-index:99999; word-break: break-all',
        'num' => 'font-weight:bold; color:#1299DA',
        'const' => 'font-weight:bold',
        'str' => 'font-weight:bold; color:#56DB3A',
        'note' => 'color:#1299DA',
        'ref' => 'color:#A0A0A0',
        'public' => 'color:#FFFFFF',
        'protected' => 'color:#FFFFFF',
        'private' => 'color:#FFFFFF',
        'meta' => 'color:#B729D9',
        'key' => 'color:#56DB3A',
        'index' => 'color:#1299DA',
        'ellipsis' => 'color:#FF8400',
    );

    private $displayOptions = array(
        'maxDepth' => 1,
        'maxStringLength' => 160,
        'fileLinkFormat' => null,
    );
    private $extraDisplayOptions = array();

    /**
     * {@inheritdoc}
     */
    public function __construct($output = null, string $charset = null, int $flags = 0)
    {
        AbstractDumper::__construct($output, $charset, $flags);
        $this->dumpId = 'php-dump-' . mt_rand();
        $this->displayOptions['fileLinkFormat'] = ini_get('xdebug.file_link_format') ?: get_cfg_var('xdebug.file_link_format');
    }

    /**
     * {@inheritdoc}
     */
    public function setStyles(array $styles)
    {
        $this->headerIsDumped = false;
        $this->styles = $styles + $this->styles;
    }

    /**
     * Configures display options.
     *
     * @param array $displayOptions A map of display options to customize the behavior
     */
    public function setDisplayOptions(array $displayOptions)
    {
        $this->headerIsDumped = false;
        $this->displayOptions = $displayOptions + $this->displayOptions;
    }

    /**
     * Sets an HTML header that will be dumped once in the output stream.
     *
     * @param string $header An HTML string
     */
    public function setDumpHeader($header)
    {
        $this->dumpHeader = $header;
    }

    /**
     * Sets an HTML prefix and suffix that will encapse every single dump.
     *
     * @param string $prefix The prepended HTML string
     * @param string $suffix The appended HTML string
     */
    public function setDumpBoundaries($prefix, $suffix)
    {
        $this->dumpPrefix = $prefix;
        $this->dumpSuffix = $suffix;
    }

    /**
     * {@inheritdoc}
     */
    public function dump(Data $data, $output = null, array $extraDisplayOptions = array()): ?string
    {
        $this->extraDisplayOptions = $extraDisplayOptions;
        $result = parent::dump($data, $output);
        $this->dumpId = 'php-dump-' . mt_rand();

        return $result;
    }

    /**
     * Dumps the HTML header.
     */
    protected function getDumpHeader()
    {
        $this->headerIsDumped = null !== $this->outputStream ? $this->outputStream : $this->lineDumper;

        if (null !== $this->dumpHeader) {
            return $this->dumpHeader;
        }

        $line = "";
        if ($this->dumpStyle) {
            $line = str_replace('{$options}', json_encode($this->displayOptions, JSON_FORCE_OBJECT), <<<'EOHTML'
                <style>
                    pre.php-dump {
                        display: block;
                        white-space: pre;
                        padding: 5px;
                    }
                    pre.php-dump:after {
                       content: "";
                       visibility: hidden;
                       display: block;
                       height: 0;
                       clear: both;
                    }
                    pre.php-dump span {
                        display: inline;
                    }
                    pre.php-dump .php-dump-compact {
                        display: none;
                    }
                    pre.php-dump abbr {
                        text-decoration: none;
                        border: none;
                        cursor: help;
                    }
                    pre.php-dump a {
                        text-decoration: none;
                        cursor: pointer;
                        border: 0;
                        outline: none;
                        color: inherit;
                    }
                    pre.php-dump .php-dump-ellipsis {
                        display: inline-block;
                        overflow: visible;
                        text-overflow: ellipsis;
                        max-width: 5em;
                        white-space: nowrap;
                        overflow: hidden;
                        vertical-align: top;
                    }
                    pre.php-dump .php-dump-ellipsis+.php-dump-ellipsis {
                        max-width: none;
                    }
                    pre.php-dump code {
                        display:inline;
                        padding:0;
                        background:none;
                    }
                    .php-dump-str-collapse .php-dump-str-collapse {
                        display: none;
                    }
                    .php-dump-str-expand .php-dump-str-expand {
                        display: none;
                    }
                    .php-dump-public.php-dump-highlight,
                    .php-dump-protected.php-dump-highlight,
                    .php-dump-private.php-dump-highlight,
                    .php-dump-str.php-dump-highlight,
                    .php-dump-key.php-dump-highlight {
                        background: rgba(111, 172, 204, 0.3);
                        border: 1px solid #7DA0B1;
                        border-radius: 3px;
                    }
                    .php-dump-public.php-dump-highlight-active,
                    .php-dump-protected.php-dump-highlight-active,
                    .php-dump-private.php-dump-highlight-active,
                    .php-dump-str.php-dump-highlight-active,
                    .php-dump-key.php-dump-highlight-active {
                        background: rgba(253, 175, 0, 0.4);
                        border: 1px solid #ffa500;
                        border-radius: 3px;
                    }
                    pre.php-dump .php-dump-search-hidden {
                        display: none;
                    }
                    pre.php-dump .php-dump-search-wrapper {
                        float: right;
                        font-size: 0;
                        white-space: nowrap;
                        max-width: 100%;
                        text-align: right;
                    }
                    pre.php-dump .php-dump-search-wrapper > * {
                        vertical-align: top;
                        box-sizing: border-box;
                        height: 21px;
                        font-weight: normal;
                        border-radius: 0;
                        background: #FFF;
                        color: #757575;
                        border: 1px solid #BBB;
                    }
                    pre.php-dump .php-dump-search-wrapper > input.php-dump-search-input {
                        padding: 3px;
                        height: 21px;
                        font-size: 12px;
                        border-right: none;
                        width: 140px;
                        border-top-left-radius: 3px;
                        border-bottom-left-radius: 3px;
                        color: #000;
                    }
                    pre.php-dump .php-dump-search-wrapper > .php-dump-search-input-next,
                    pre.php-dump .php-dump-search-wrapper > .php-dump-search-input-previous {
                        background: #F2F2F2;
                        outline: none;
                        border-left: none;
                        font-size: 0;
                        line-height: 0;
                    }
                    pre.php-dump .php-dump-search-wrapper > .php-dump-search-input-next {
                        border-top-right-radius: 3px;
                        border-bottom-right-radius: 3px;
                    }
                    pre.php-dump .php-dump-search-wrapper > .php-dump-search-input-next > svg,
                    pre.php-dump .php-dump-search-wrapper > .php-dump-search-input-previous > svg {
                        pointer-events: none;
                        width: 12px;
                        height: 12px;
                    }
                    pre.php-dump .php-dump-search-wrapper > .php-dump-search-count {
                        display: inline-block;
                        padding: 0 5px;
                        margin: 0;
                        border-left: none;
                        line-height: 21px;
                        font-size: 12px;
                    }
EOHTML
            );

            foreach ($this->styles as $class => $style) {
                $line .= 'pre.php-dump' . ('default' === $class ? ', pre.php-dump' : '') . ' .php-dump-' . $class . '{' . $style . '}';
            }
            $line = preg_replace('/\s+/', ' ', $line) . '</style>';
        }


        return $this->dumpHeader = $line . $this->dumpHeader;
    }

    /**
     * {@inheritdoc}
     */
    public function enterHash(Cursor $cursor, $type, $class, $hasChild)
    {
        parent::enterHash($cursor, $type, $class, false);

        if ($cursor->skipChildren) {
            $cursor->skipChildren = false;
            $eol = ' class=php-dump-compact>';
        } elseif ($this->expandNextHash) {
            $this->expandNextHash = false;
            $eol = ' class=php-dump-expanded>';
        } else {
            $eol = '>';
        }

        if ($hasChild) {
            $this->line .= '<samp';
            if ($cursor->refIndex) {
                $r = Cursor::HASH_OBJECT !== $type ? 1 - (Cursor::HASH_RESOURCE !== $type) : 2;
                $r .= $r && 0 < $cursor->softRefHandle ? $cursor->softRefHandle : $cursor->refIndex;

                $this->line .= sprintf(' id=%s-ref%s', $this->dumpId, $r);
            }
            $this->line .= $eol;
            $this->dumpLine($cursor->depth);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function leaveHash(Cursor $cursor, $type, $class, $hasChild, $cut)
    {
        $this->dumpEllipsis($cursor, $hasChild, $cut);
        if ($hasChild) {
            $this->line .= '</samp>';
        }
        parent::leaveHash($cursor, $type, $class, $hasChild, 0);
    }

    /**
     * {@inheritdoc}
     */
    protected function style(string $style, string $value, array $attr = array()): string
    {
        if ('' === $value) {
            return '';
        }

        $v = esc($value);

        if ('ref' === $style) {
            if (empty($attr['count'])) {
                return sprintf('<a class=php-dump-ref>%s</a>', $v);
            }
            $r = ('#' !== $v[0] ? 1 - ('@' !== $v[0]) : 2) . substr($value, 1);

            return sprintf('<a class=php-dump-ref href=#%s-ref%s title="%d occurrences">%s</a>', $this->dumpId, $r, 1 + $attr['count'], $v);
        }

        if ('const' === $style && isset($attr['value'])) {
            $style .= sprintf(' title="%s"', esc(is_scalar($attr['value']) ? $attr['value'] : json_encode($attr['value'])));
        } elseif ('public' === $style) {
            $style .= sprintf(' title="%s"', empty($attr['dynamic']) ? 'Public property' : 'Runtime added dynamic property');
        } elseif ('str' === $style && 1 < $attr['length']) {
            $style .= sprintf(' title="%d%s characters"', $attr['length'], $attr['binary'] ? ' binary or non-UTF-8' : '');
        } elseif ('note' === $style && false !== $c = strrpos($v, '\\')) {
            return sprintf('<abbr title="%s" class=php-dump-%s>%s</abbr>', $v, $style, substr($v, $c + 1));
        } elseif ('protected' === $style) {
            $style .= ' title="Protected property"';
        } elseif ('meta' === $style && isset($attr['title'])) {
            $style .= sprintf(' title="%s"', esc($this->utf8Encode($attr['title'])));
        } elseif ('private' === $style) {
            $style .= sprintf(' title="Private property defined in class:&#10;`%s`"', esc($this->utf8Encode($attr['class'])));
        }
        $map = static::$controlCharsMap;

        if (isset($attr['ellipsis'])) {
            $class = 'php-dump-ellipsis';
            if (isset($attr['ellipsis-type'])) {
                $class = sprintf('"%s php-dump-ellipsis-%s"', $class, $attr['ellipsis-type']);
            }
            $label = esc(substr($value, -$attr['ellipsis']));
            $style = str_replace(' title="', " title=\"$v\n", $style);
            $v = sprintf('<span class=%s>%s</span>', $class, substr($v, 0, -\strlen($label)));

            if (!empty($attr['ellipsis-tail'])) {
                $tail = \strlen(esc(substr($value, -$attr['ellipsis'], $attr['ellipsis-tail'])));
                $v .= sprintf('<span class=php-dump-ellipsis>%s</span>%s', substr($label, 0, $tail), substr($label, $tail));
            } else {
                $v .= $label;
            }
        }

        $v = "<span class=php-dump-{$style}>" . preg_replace_callback(static::$controlCharsRx, function ($c) use ($map) {
                $s = '<span class=php-dump-default>';
                $c = $c[$i = 0];
                do {
                    $s .= isset($map[$c[$i]]) ? $map[$c[$i]] : sprintf('\x%02X', \ord($c[$i]));
                } while (isset($c[++$i]));

                return $s . '</span>';
            }, $v) . '</span>';

        if (isset($attr['file']) && $href = $this->getSourceLink($attr['file'], isset($attr['line']) ? $attr['line'] : 0)) {
            $attr['href'] = $href;
        }
        if (isset($attr['href'])) {
            $target = isset($attr['file']) ? '' : ' target="_blank"';
            $v = sprintf('<a href="%s"%s rel="noopener noreferrer">%s</a>', esc($this->utf8Encode($attr['href'])), $target, $v);
        }
        if (isset($attr['lang'])) {
            $v = sprintf('<code class="%s">%s</code>', esc($attr['lang']), $v);
        }

        return $v;
    }

    /**
     * {@inheritdoc}
     */
    protected function dumpLine($depth, $endOfValue = false)
    {
        if (-1 === $this->lastDepth) {
            $this->line = sprintf($this->dumpPrefix, $this->dumpId, $this->indentPad) . $this->line;
        }
        if ($this->headerIsDumped !== (null !== $this->outputStream ? $this->outputStream : $this->lineDumper)) {
            $this->line = $this->getDumpHeader() . $this->line;
        }

        if (-1 === $depth) {
            $args = array('"' . $this->dumpId . '"');
            if ($this->extraDisplayOptions) {
                $args[] = json_encode($this->extraDisplayOptions, JSON_FORCE_OBJECT);
            }
            // Replace is for BC
            $this->line .= sprintf(str_replace('"%s"', '%s', $this->dumpSuffix), implode(', ', $args));
        }
        $this->lastDepth = $depth;

        $this->line = mb_convert_encoding($this->line, 'HTML-ENTITIES', 'UTF-8');

        if (-1 === $depth) {
            AbstractDumper::dumpLine(0);
        }
        AbstractDumper::dumpLine($depth);
    }

    private function getSourceLink($file, $line)
    {
        $options = $this->extraDisplayOptions + $this->displayOptions;

        if ($fmt = $options['fileLinkFormat']) {
            return \is_string($fmt) ? strtr($fmt, array('%f' => $file, '%l' => $line)) : $fmt->format($file, $line);
        }

        return false;
    }
}

function esc($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
