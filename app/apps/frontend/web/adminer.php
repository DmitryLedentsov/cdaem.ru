<?php
/** Adminer - Compact database management
 * @link https://www.adminer.org/
 * @author Jakub Vrana, https://www.vrana.cz/
 * @copyright 2007 Jakub Vrana
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
 * @version 4.6.3
 */
error_reporting(6135);
$Wc = !preg_match('~^(unsafe_raw)?$~', ini_get("filter.default"));
if ($Wc || ini_get("filter.default_flags")) {
    foreach (['_GET', '_POST', '_COOKIE', '_SERVER'] as $X) {
        $Di = filter_input_array(constant("INPUT$X"), FILTER_UNSAFE_RAW);
        if ($Di) {
            $$X = $Di;
        }
    }
}
if (function_exists("mb_internal_encoding")) {
    mb_internal_encoding("8bit");
}
function connection()
{
    global $g;

    return $g;
}

function adminer()
{
    global $b;

    return $b;
}

function version()
{
    global $ia;

    return $ia;
}

function idf_unescape($u)
{
    $ne = substr($u, -1);

    return
        str_replace($ne . $ne, $ne, substr($u, 1, -1));
}

function escape_string($X)
{
    return
        substr(q($X), 1, -1);
}

function number($X)
{
    return
        preg_replace('~[^0-9]+~', '', $X);
}

function number_type()
{
    return '((?<!o)int(?!er)|numeric|real|float|double|decimal|money)';
}

function remove_slashes($ng, $Wc = false)
{
    if (get_magic_quotes_gpc()) {
        while (list($y, $X) = each($ng)) {
            foreach ($X
                     as $ce => $W) {
                unset($ng[$y][$ce]);
                if (is_array($W)) {
                    $ng[$y][stripslashes($ce)] = $W;
                    $ng[] =& $ng[$y][stripslashes($ce)];
                } else {
                    $ng[$y][stripslashes($ce)] = ($Wc ? $W : stripslashes($W));
                }
            }
        }
    }
}

function bracket_escape($u, $Oa = false)
{
    static $oi = [':' => ':1', ']' => ':2', '[' => ':3', '"' => ':4'];

    return
        strtr($u, ($Oa ? array_flip($oi) : $oi));
}

function min_version($Ui, $Ae = "", $h = null)
{
    global $g;
    if (!$h) {
        $h = $g;
    }
    $ih = $h->server_info;
    if ($Ae && preg_match('~([\d.]+)-MariaDB~', $ih, $B)) {
        $ih = $B[1];
        $Ui = $Ae;
    }

    return (version_compare($ih, $Ui) >= 0);
}

function charset($g)
{
    return (min_version("5.5.3", 0, $g) ? "utf8mb4" : "utf8");
}

function script($sh, $ni = "\n")
{
    return "<script" . nonce() . ">$sh</script>$ni";
}

function script_src($Ii)
{
    return "<script src='" . h($Ii) . "'" . nonce() . "></script>\n";
}

function nonce()
{
    return ' nonce="' . get_nonce() . '"';
}

function target_blank()
{
    return ' target="_blank" rel="noreferrer noopener"';
}

function h($Q)
{
    return
        str_replace("\0", "&#0;", htmlspecialchars($Q, ENT_QUOTES, 'utf-8'));
}

function nl_br($Q)
{
    return
        str_replace("\n", "<br>", $Q);
}

function checkbox($C, $Y, $fb, $je = "", $pf = "", $kb = "", $ke = "")
{
    $I = "<input type='checkbox' name='$C' value='" . h($Y) . "'" . ($fb ? " checked" : "") . ($ke ? " aria-labelledby='$ke'" : "") . ">" . ($pf ? script("qsl('input').onclick = function () { $pf };", "") : "");

    return ($je != "" || $kb ? "<label" . ($kb ? " class='$kb'" : "") . ">$I" . h($je) . "</label>" : $I);
}

function optionlist($vf, $ch = null, $Mi = false)
{
    $I = "";
    foreach ($vf
             as $ce => $W) {
        $wf = [$ce => $W];
        if (is_array($W)) {
            $I .= '<optgroup label="' . h($ce) . '">';
            $wf = $W;
        }
        foreach ($wf
                 as $y => $X) {
            $I .= '<option' . ($Mi || is_string($y) ? ' value="' . h($y) . '"' : '') . (($Mi || is_string($y) ? (string)$y : $X) === $ch ? ' selected' : '') . '>' . h($X);
        }
        if (is_array($W)) {
            $I .= '</optgroup>';
        }
    }

    return $I;
}

function html_select($C, $vf, $Y = "", $of = true, $ke = "")
{
    if ($of) {
        return "<select name='" . h($C) . "'" . ($ke ? " aria-labelledby='$ke'" : "") . ">" . optionlist($vf, $Y) . "</select>" . (is_string($of) ? script("qsl('select').onchange = function () { $of };", "") : "");
    }
    $I = "";
    foreach ($vf
             as $y => $X) {
        $I .= "<label><input type='radio' name='" . h($C) . "' value='" . h($y) . "'" . ($y == $Y ? " checked" : "") . ">" . h($X) . "</label>";
    }

    return $I;
}

function select_input($Ka, $vf, $Y = "", $of = "", $Zf = "")
{
    $Sh = ($vf ? "select" : "input");

    return "<$Sh$Ka" . ($vf ? "><option value=''>$Zf" . optionlist($vf, $Y, true) . "</select>" : " size='10' value='" . h($Y) . "' placeholder='$Zf'>") . ($of ? script("qsl('$Sh').onchange = $of;", "") : "");
}

function confirm($Ke = "", $dh = "qsl('input')")
{
    return
        script("$dh.onclick = function () { return confirm('" . ($Ke ? js_escape($Ke) : lang(0)) . "'); };", "");
}

function print_fieldset($t, $se, $Xi = false)
{
    echo "<fieldset><legend>", "<a href='#fieldset-$t'>$se</a>", script("qsl('a').onclick = partial(toggle, 'fieldset-$t');", ""), "</legend>", "<div id='fieldset-$t'" . ($Xi ? "" : " class='hidden'") . ">\n";
}

function bold($Wa, $kb = "")
{
    return ($Wa ? " class='active $kb'" : ($kb ? " class='$kb'" : ""));
}

function odd($I = ' class="odd"')
{
    static $s = 0;
    if (!$I) {
        $s = -1;
    }

    return ($s++ % 2 ? $I : '');
}

function js_escape($Q)
{
    return
        addcslashes($Q, "\r\n'\\/");
}

function json_row($y, $X = null)
{
    static $Xc = true;
    if ($Xc) {
        echo "{";
    }
    if ($y != "") {
        echo($Xc ? "" : ",") . "\n\t\"" . addcslashes($y, "\r\n\t\"\\/") . '": ' . ($X !== null ? '"' . addcslashes($X, "\r\n\"\\/") . '"' : 'null');
        $Xc = false;
    } else {
        echo "\n}\n";
        $Xc = true;
    }
}

function ini_bool($Pd)
{
    $X = ini_get($Pd);

    return (preg_match('~^(on|true|yes)$~i', $X) || (int)$X);
}

function sid()
{
    static $I;
    if ($I === null) {
        $I = (SID && !($_COOKIE && ini_bool("session.use_cookies")));
    }

    return $I;
}

function set_password($Ti, $N, $V, $F)
{
    $_SESSION["pwds"][$Ti][$N][$V] = ($_COOKIE["adminer_key"] && is_string($F) ? [encrypt_string($F, $_COOKIE["adminer_key"])] : $F);
}

function get_password()
{
    $I = get_session("pwds");
    if (is_array($I)) {
        $I = ($_COOKIE["adminer_key"] ? decrypt_string($I[0], $_COOKIE["adminer_key"]) : false);
    }

    return $I;
}

function q($Q)
{
    global $g;

    return $g->quote($Q);
}

function get_vals($G, $d = 0)
{
    global $g;
    $I = [];
    $H = $g->query($G);
    if (is_object($H)) {
        while ($J = $H->fetch_row()) {
            $I[] = $J[$d];
        }
    }

    return $I;
}

function get_key_vals($G, $h = null, $lh = true)
{
    global $g;
    if (!is_object($h)) {
        $h = $g;
    }
    $I = [];
    $H = $h->query($G);
    if (is_object($H)) {
        while ($J = $H->fetch_row()) {
            if ($lh) {
                $I[$J[0]] = $J[1];
            } else {
                $I[] = $J[0];
            }
        }
    }

    return $I;
}

function get_rows($G, $h = null, $n = "<p class='error'>")
{
    global $g;
    $yb = (is_object($h) ? $h : $g);
    $I = [];
    $H = $yb->query($G);
    if (is_object($H)) {
        while ($J = $H->fetch_assoc()) {
            $I[] = $J;
        }
    } elseif (!$H && !is_object($h) && $n && defined("PAGE_HEADER")) {
        echo $n . error() . "\n";
    }

    return $I;
}

function unique_array($J, $w)
{
    foreach ($w
             as $v) {
        if (preg_match("~PRIMARY|UNIQUE~", $v["type"])) {
            $I = [];
            foreach ($v["columns"] as $y) {
                if (!isset($J[$y])) {
                    continue
                2;
                }
                $I[$y] = $J[$y];
            }

            return $I;
        }
    }
}

function escape_key($y)
{
    if (preg_match('(^([\w(]+)(' . str_replace("_", ".*", preg_quote(idf_escape("_"))) . ')([ \w)]+)$)', $y, $B)) {
        return $B[1] . idf_escape(idf_unescape($B[2])) . $B[3];
    }

    return
        idf_escape($y);
}

function where($Z, $p = [])
{
    global $g, $x;
    $I = [];
    foreach ((array)$Z["where"] as $y => $X) {
        $y = bracket_escape($y, 1);
        $d = escape_key($y);
        $I[] = $d . ($x == "sql" && preg_match('~^[0-9]*\.[0-9]*$~', $X) ? " LIKE " . q(addcslashes($X, "%_\\")) : ($x == "mssql" ? " LIKE " . q(preg_replace('~[_%[]~', '[\0]', $X)) : " = " . unconvert_field($p[$y], q($X))));
        if ($x == "sql" && preg_match('~char|text~', $p[$y]["type"]) && preg_match("~[^ -@]~", $X)) {
            $I[] = "$d = " . q($X) . " COLLATE " . charset($g) . "_bin";
        }
    }
    foreach ((array)$Z["null"] as $y) {
        $I[] = escape_key($y) . " IS NULL";
    }

    return
        implode(" AND ", $I);
}

function where_check($X, $p = [])
{
    parse_str($X, $db);
    remove_slashes([&$db]);

    return
        where($db, $p);
}

function where_link($s, $d, $Y, $rf = "=")
{
    return "&where%5B$s%5D%5Bcol%5D=" . urlencode($d) . "&where%5B$s%5D%5Bop%5D=" . urlencode(($Y !== null ? $rf : "IS NULL")) . "&where%5B$s%5D%5Bval%5D=" . urlencode($Y);
}

function convert_fields($e, $p, $L = [])
{
    $I = "";
    foreach ($e
             as $y => $X) {
        if ($L && !in_array(idf_escape($y), $L)) {
            continue;
        }
        $Ha = convert_field($p[$y]);
        if ($Ha) {
            $I .= ", $Ha AS " . idf_escape($y);
        }
    }

    return $I;
}

function cookie($C, $Y, $ve = 2592000)
{
    global $ba;

    return
        header("Set-Cookie: $C=" . urlencode($Y) . ($ve ? "; expires=" . gmdate("D, d M Y H:i:s", time() + $ve) . " GMT" : "") . "; path=" . preg_replace('~\?.*~', '', $_SERVER["REQUEST_URI"]) . ($ba ? "; secure" : "") . "; HttpOnly; SameSite=lax", false);
}

function restart_session()
{
    if (!ini_bool("session.use_cookies")) {
        session_start();
    }
}

function stop_session($cd = false)
{
    if (!ini_bool("session.use_cookies") || ($cd && @ini_set("session.use_cookies", false) !== false)) {
        session_write_close();
    }
}

function &get_session($y)
{
    return $_SESSION[$y][DRIVER][SERVER][$_GET["username"]];
}

function set_session($y, $X)
{
    $_SESSION[$y][DRIVER][SERVER][$_GET["username"]] = $X;
}

function auth_url($Ti, $N, $V, $l = null)
{
    global $fc;
    preg_match('~([^?]*)\??(.*)~', remove_from_uri(implode("|", array_keys($fc)) . "|username|" . ($l !== null ? "db|" : "") . session_name()), $B);

    return "$B[1]?" . (sid() ? SID . "&" : "") . ($Ti != "server" || $N != "" ? urlencode($Ti) . "=" . urlencode($N) . "&" : "") . "username=" . urlencode($V) . ($l != "" ? "&db=" . urlencode($l) : "") . ($B[2] ? "&$B[2]" : "");
}

function is_ajax()
{
    return ($_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest");
}

function redirect($A, $Ke = null)
{
    if ($Ke !== null) {
        restart_session();
        $_SESSION["messages"][preg_replace('~^[^?]*~', '', ($A !== null ? $A : $_SERVER["REQUEST_URI"]))][] = $Ke;
    }
    if ($A !== null) {
        if ($A == "") {
            $A = ".";
        }
        header("Location: $A");
        exit;
    }
}

function query_redirect($G, $A, $Ke, $zg = true, $Dc = true, $Oc = false, $ai = "")
{
    global $g, $n, $b;
    if ($Dc) {
        $_h = microtime(true);
        $Oc = !$g->query($G);
        $ai = format_time($_h);
    }
    $vh = "";
    if ($G) {
        $vh = $b->messageQuery($G, $ai, $Oc);
    }
    if ($Oc) {
        $n = error() . $vh . script("messagesPrint();");

        return
            false;
    }
    if ($zg) {
        redirect($A, $Ke . $vh);
    }

    return
        true;
}

function queries($G)
{
    global $g;
    static $sg = [];
    static $_h;
    if (!$_h) {
        $_h = microtime(true);
    }
    if ($G === null) {
        return
        [implode("\n", $sg), format_time($_h)];
    }
    $sg[] = (preg_match('~;$~', $G) ? "DELIMITER ;;\n$G;\nDELIMITER " : $G) . ";";

    return $g->query($G);
}

function apply_queries($G, $T, $_c = 'table')
{
    foreach ($T
             as $R) {
        if (!queries("$G " . $_c($R))) {
            return
            false;
        }
    }

    return
        true;
}

function queries_redirect($A, $Ke, $zg)
{
    list($sg, $ai) = queries(null);

    return
        query_redirect($sg, $A, $Ke, $zg, false, !$zg, $ai);
}

function format_time($_h)
{
    return
        lang(1, max(0, microtime(true) - $_h));
}

function remove_from_uri($Kf = "")
{
    return
        substr(preg_replace("~(?<=[?&])($Kf" . (SID ? "" : "|" . session_name()) . ")=[^&]*&~", '', "$_SERVER[REQUEST_URI]&"), 0, -1);
}

function pagination($E, $Kb)
{
    return " " . ($E == $Kb ? $E + 1 : '<a href="' . h(remove_from_uri("page") . ($E ? "&page=$E" . ($_GET["next"] ? "&next=" . urlencode($_GET["next"]) : "") : "")) . '">' . ($E + 1) . "</a>");
}

function get_file($y, $Sb = false)
{
    $Uc = $_FILES[$y];
    if (!$Uc) {
        return
        null;
    }
    foreach ($Uc
             as $y => $X) {
        $Uc[$y] = (array)$X;
    }
    $I = '';
    foreach ($Uc["error"] as $y => $n) {
        if ($n) {
            return $n;
        }
        $C = $Uc["name"][$y];
        $ii = $Uc["tmp_name"][$y];
        $_b = file_get_contents($Sb && preg_match('~\.gz$~', $C) ? "compress.zlib://$ii" : $ii);
        if ($Sb) {
            $_h = substr($_b, 0, 3);
            if (function_exists("iconv") && preg_match("~^\xFE\xFF|^\xFF\xFE~", $_h, $Eg)) {
                $_b = iconv("utf-16", "utf-8", $_b);
            } elseif ($_h == "\xEF\xBB\xBF") {
                $_b = substr($_b, 3);
            }
            $I .= $_b . "\n\n";
        } else {
            $I .= $_b;
        }
    }

    return $I;
}

function upload_error($n)
{
    $He = ($n == UPLOAD_ERR_INI_SIZE ? ini_get("upload_max_filesize") : 0);

    return ($n ? lang(2) . ($He ? " " . lang(3, $He) : "") : lang(4));
}

function repeat_pattern($Xf, $te)
{
    return
        str_repeat("$Xf{0,65535}", $te / 65535) . "$Xf{0," . ($te % 65535) . "}";
}

function is_utf8($X)
{
    return (preg_match('~~u', $X) && !preg_match('~[\0-\x8\xB\xC\xE-\x1F]~', $X));
}

function shorten_utf8($Q, $te = 80, $Gh = "")
{
    if (!preg_match("(^(" . repeat_pattern("[\t\r\n -\x{10FFFF}]", $te) . ")($)?)u", $Q, $B)) {
        preg_match("(^(" . repeat_pattern("[\t\r\n -~]", $te) . ")($)?)", $Q, $B);
    }

    return
        h($B[1]) . $Gh . (isset($B[2]) ? "" : "<i>...</i>");
}

function format_number($X)
{
    return
        strtr(number_format($X, 0, ".", lang(5)), preg_split('~~u', lang(6), -1, PREG_SPLIT_NO_EMPTY));
}

function friendly_url($X)
{
    return
        preg_replace('~[^a-z0-9_]~i', '-', $X);
}

function hidden_fields($ng, $Fd = [])
{
    $I = false;
    while (list($y, $X) = each($ng)) {
        if (!in_array($y, $Fd)) {
            if (is_array($X)) {
                foreach ($X
                         as $ce => $W) {
                    $ng[$y . "[$ce]"] = $W;
                }
            } else {
                $I = true;
                echo '<input type="hidden" name="' . h($y) . '" value="' . h($X) . '">';
            }
        }
    }

    return $I;
}

function hidden_fields_get()
{
    echo(sid() ? '<input type="hidden" name="' . session_name() . '" value="' . h(session_id()) . '">' : ''), (SERVER !== null ? '<input type="hidden" name="' . DRIVER . '" value="' . h(SERVER) . '">' : ""), '<input type="hidden" name="username" value="' . h($_GET["username"]) . '">';
}

function table_status1($R, $Pc = false)
{
    $I = table_status($R, $Pc);

    return ($I ? $I : ["Name" => $R]);
}

function column_foreign_keys($R)
{
    global $b;
    $I = [];
    foreach ($b->foreignKeys($R) as $q) {
        foreach ($q["source"] as $X) {
            $I[$X][] = $q;
        }
    }

    return $I;
}

function enum_input($U, $Ka, $o, $Y, $uc = null)
{
    global $b;
    preg_match_all("~'((?:[^']|'')*)'~", $o["length"], $Ce);
    $I = ($uc !== null ? "<label><input type='$U'$Ka value='$uc'" . ((is_array($Y) ? in_array($uc, $Y) : $Y === 0) ? " checked" : "") . "><i>" . lang(7) . "</i></label>" : "");
    foreach ($Ce[1] as $s => $X) {
        $X = stripcslashes(str_replace("''", "'", $X));
        $fb = (is_int($Y) ? $Y == $s + 1 : (is_array($Y) ? in_array($s + 1, $Y) : $Y === $X));
        $I .= " <label><input type='$U'$Ka value='" . ($s + 1) . "'" . ($fb ? ' checked' : '') . '>' . h($b->editVal($X, $o)) . '</label>';
    }

    return $I;
}

function input($o, $Y, $r)
{
    global $zi, $b, $x;
    $C = h(bracket_escape($o["field"]));
    echo "<td class='function'>";
    if (is_array($Y) && !$r) {
        $Fa = [$Y];
        if (version_compare(PHP_VERSION, 5.4) >= 0) {
            $Fa[] = JSON_PRETTY_PRINT;
        }
        $Y = call_user_func_array('json_encode', $Fa);
        $r = "json";
    }
    $Ig = ($x == "mssql" && $o["auto_increment"]);
    if ($Ig && !$_POST["save"]) {
        $r = null;
    }
    $ld = (isset($_GET["select"]) || $Ig ? ["orig" => lang(8)] : []) + $b->editFunctions($o);
    $Ka = " name='fields[$C]'";
    if ($o["type"] == "enum") {
        echo
        h($ld[""]) . "<td>" . $b->editInput($_GET["edit"], $o, $Ka, $Y);
    } else {
        $vd = (in_array($r, $ld) || isset($ld[$r]));
        echo(count($ld) > 1 ? "<select name='function[$C]'>" . optionlist($ld, $r === null || $vd ? $r : "") . "</select>" . on_help("getTarget(event).value.replace(/^SQL\$/, '')", 1) . script("qsl('select').onchange = functionChange;", "") : h(reset($ld))) . '<td>';
        $Rd = $b->editInput($_GET["edit"], $o, $Ka, $Y);
        if ($Rd != "") {
            echo $Rd;
        } elseif (preg_match('~bool~', $o["type"])) {
            echo "<input type='hidden'$Ka value='0'>" . "<input type='checkbox'" . (preg_match('~^(1|t|true|y|yes|on)$~i', $Y) ? " checked='checked'" : "") . "$Ka value='1'>";
        } elseif ($o["type"] == "set") {
            preg_match_all("~'((?:[^']|'')*)'~", $o["length"], $Ce);
            foreach ($Ce[1] as $s => $X) {
                $X = stripcslashes(str_replace("''", "'", $X));
                $fb = (is_int($Y) ? ($Y >> $s) & 1 : in_array($X, explode(",", $Y), true));
                echo " <label><input type='checkbox' name='fields[$C][$s]' value='" . (1 << $s) . "'" . ($fb ? ' checked' : '') . ">" . h($b->editVal($X, $o)) . '</label>';
            }
        } elseif (preg_match('~blob|bytea|raw|file~', $o["type"]) && ini_bool("file_uploads")) {
            echo "<input type='file' name='fields-$C'>";
        } elseif (($Yh = preg_match('~text|lob~', $o["type"])) || preg_match("~\n~", $Y)) {
            if ($Yh && $x != "sqlite") {
                $Ka .= " cols='50' rows='12'";
            } else {
                $K = min(12, substr_count($Y, "\n") + 1);
                $Ka .= " cols='30' rows='$K'" . ($K == 1 ? " style='height: 1.2em;'" : "");
            }
            echo "<textarea$Ka>" . h($Y) . '</textarea>';
        } elseif ($r == "json" || preg_match('~^jsonb?$~', $o["type"])) {
            echo "<textarea$Ka cols='50' rows='12' class='jush-js'>" . h($Y) . '</textarea>';
        } else {
            $Je = (!preg_match('~int~', $o["type"]) && preg_match('~^(\d+)(,(\d+))?$~', $o["length"], $B) ? ((preg_match("~binary~", $o["type"]) ? 2 : 1) * $B[1] + ($B[3] ? 1 : 0) + ($B[2] && !$o["unsigned"] ? 1 : 0)) : ($zi[$o["type"]] ? $zi[$o["type"]] + ($o["unsigned"] ? 0 : 1) : 0));
            if ($x == 'sql' && min_version(5.6) && preg_match('~time~', $o["type"])) {
                $Je += 7;
            }
            echo "<input" . ((!$vd || $r === "") && preg_match('~(?<!o)int(?!er)~', $o["type"]) && !preg_match('~\[\]~', $o["full_type"]) ? " type='number'" : "") . " value='" . h($Y) . "'" . ($Je ? " data-maxlength='$Je'" : "") . (preg_match('~char|binary~', $o["type"]) && $Je > 20 ? " size='40'" : "") . "$Ka>";
        }
        echo $b->editHint($_GET["edit"], $o, $Y);
        $Xc = 0;
        foreach ($ld
                 as $y => $X) {
            if ($y === "" || !$X) {
                break;
            }
            $Xc++;
        }
        if ($Xc) {
            echo
        script("mixin(qsl('td'), {onchange: partial(skipOriginal, $Xc), oninput: function () { this.onchange(); }});");
        }
    }
}

function process_input($o)
{
    global $b, $m;
    $u = bracket_escape($o["field"]);
    $r = $_POST["function"][$u];
    $Y = $_POST["fields"][$u];
    if ($o["type"] == "enum") {
        if ($Y == -1) {
            return
            false;
        }
        if ($Y == "") {
            return "NULL";
        }

        return +$Y;
    }
    if ($o["auto_increment"] && $Y == "") {
        return
        null;
    }
    if ($r == "orig") {
        return ($o["on_update"] == "CURRENT_TIMESTAMP" ? idf_escape($o["field"]) : false);
    }
    if ($r == "NULL") {
        return "NULL";
    }
    if ($o["type"] == "set") {
        return
        array_sum((array)$Y);
    }
    if ($r == "json") {
        $r = "";
        $Y = json_decode($Y, true);
        if (!is_array($Y)) {
            return
            false;
        }

        return $Y;
    }
    if (preg_match('~blob|bytea|raw|file~', $o["type"]) && ini_bool("file_uploads")) {
        $Uc = get_file("fields-$u");
        if (!is_string($Uc)) {
            return
            false;
        }

        return $m->quoteBinary($Uc);
    }

    return $b->processInput($o, $Y, $r);
}

function fields_from_edit()
{
    global $m;
    $I = [];
    foreach ((array)$_POST["field_keys"] as $y => $X) {
        if ($X != "") {
            $X = bracket_escape($X);
            $_POST["function"][$X] = $_POST["field_funs"][$y];
            $_POST["fields"][$X] = $_POST["field_vals"][$y];
        }
    }
    foreach ((array)$_POST["fields"] as $y => $X) {
        $C = bracket_escape($y, 1);
        $I[$C] = ["field" => $C, "privileges" => ["insert" => 1, "update" => 1], "null" => 1, "auto_increment" => ($y == $m->primary),];
    }

    return $I;
}

function search_tables()
{
    global $b, $g;
    $_GET["where"][0]["val"] = $_POST["query"];
    $fh = "<ul>\n";
    foreach (table_status('', true) as $R => $S) {
        $C = $b->tableName($S);
        if (isset($S["Engine"]) && $C != "" && (!$_POST["tables"] || in_array($R, $_POST["tables"]))) {
            $H = $g->query("SELECT" . limit("1 FROM " . table($R), " WHERE " . implode(" AND ", $b->selectSearchProcess(fields($R), [])), 1));
            if (!$H || $H->fetch_row()) {
                $jg = "<a href='" . h(ME . "select=" . urlencode($R) . "&where[0][op]=" . urlencode($_GET["where"][0]["op"]) . "&where[0][val]=" . urlencode($_GET["where"][0]["val"])) . "'>$C</a>";
                echo "$fh<li>" . ($H ? $jg : "<p class='error'>$jg: " . error()) . "\n";
                $fh = "";
            }
        }
    }
    echo($fh ? "<p class='message'>" . lang(9) : "</ul>") . "\n";
}

function dump_headers($Dd, $Te = false)
{
    global $b;
    $I = $b->dumpHeaders($Dd, $Te);
    $Hf = $_POST["output"];
    if ($Hf != "text") {
        header("Content-Disposition: attachment; filename=" . $b->dumpFilename($Dd) . ".$I" . ($Hf != "file" && !preg_match('~[^0-9a-z]~', $Hf) ? ".$Hf" : ""));
    }
    session_write_close();
    ob_flush();
    flush();

    return $I;
}

function dump_csv($J)
{
    foreach ($J
             as $y => $X) {
        if (preg_match("~[\"\n,;\t]~", $X) || $X === "") {
            $J[$y] = '"' . str_replace('"', '""', $X) . '"';
        }
    }
    echo
        implode(($_POST["format"] == "csv" ? "," : ($_POST["format"] == "tsv" ? "\t" : ";")), $J) . "\r\n";
}

function apply_sql_function($r, $d)
{
    return ($r ? ($r == "unixepoch" ? "DATETIME($d, '$r')" : ($r == "count distinct" ? "COUNT(DISTINCT " : strtoupper("$r(")) . "$d)") : $d);
}

function get_temp_dir()
{
    $I = ini_get("upload_tmp_dir");
    if (!$I) {
        if (function_exists('sys_get_temp_dir')) {
            $I = sys_get_temp_dir();
        } else {
            $Vc = @tempnam("", "");
            if (!$Vc) {
                return
                false;
            }
            $I = dirname($Vc);
            unlink($Vc);
        }
    }

    return $I;
}

function file_open_lock($Vc)
{
    $jd = @fopen($Vc, "r+");
    if (!$jd) {
        $jd = @fopen($Vc, "w");
        if (!$jd) {
            return;
        }
        chmod($Vc, 0660);
    }
    flock($jd, LOCK_EX);

    return $jd;
}

function file_write_unlock($jd, $Mb)
{
    rewind($jd);
    fwrite($jd, $Mb);
    ftruncate($jd, strlen($Mb));
    flock($jd, LOCK_UN);
    fclose($jd);
}

function password_file($i)
{
    $Vc = get_temp_dir() . "/adminer.key";
    $I = @file_get_contents($Vc);
    if ($I || !$i) {
        return $I;
    }
    $jd = @fopen($Vc, "w");
    if ($jd) {
        chmod($Vc, 0660);
        $I = rand_string();
        fwrite($jd, $I);
        fclose($jd);
    }

    return $I;
}

function rand_string()
{
    return
        md5(uniqid(mt_rand(), true));
}

function select_value($X, $_, $o, $Zh)
{
    global $b;
    if (is_array($X)) {
        $I = "";
        foreach ($X
                 as $ce => $W) {
            $I .= "<tr>" . ($X != array_values($X) ? "<th>" . h($ce) : "") . "<td>" . select_value($W, $_, $o, $Zh);
        }

        return "<table cellspacing='0'>$I</table>";
    }
    if (!$_) {
        $_ = $b->selectLink($X, $o);
    }
    if ($_ === null) {
        if (is_mail($X)) {
            $_ = "mailto:$X";
        }
        if (is_url($X)) {
            $_ = $X;
        }
    }
    $I = $b->editVal($X, $o);
    if ($I !== null) {
        if (!is_utf8($I)) {
            $I = "\0";
        } elseif ($Zh != "" && is_shortable($o)) {
            $I = shorten_utf8($I, max(0, +$Zh));
        } else {
            $I = h($I);
        }
    }

    return $b->selectVal($I, $_, $o, $X);
}

function is_mail($rc)
{
    $Ia = '[-a-z0-9!#$%&\'*+/=?^_`{|}~]';
    $ec = '[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';
    $Xf = "$Ia+(\\.$Ia+)*@($ec?\\.)+$ec";

    return
        is_string($rc) && preg_match("(^$Xf(,\\s*$Xf)*\$)i", $rc);
}

function is_url($Q)
{
    $ec = '[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';

    return
        preg_match("~^(https?)://($ec?\\.)+$ec(:\\d+)?(/.*)?(\\?.*)?(#.*)?\$~i", $Q);
}

function is_shortable($o)
{
    return
        preg_match('~char|text|json|lob|geometry|point|linestring|polygon|string|bytea~', $o["type"]);
}

function count_rows($R, $Z, $Xd, $od)
{
    global $x;
    $G = " FROM " . table($R) . ($Z ? " WHERE " . implode(" AND ", $Z) : "");

    return ($Xd && ($x == "sql" || count($od) == 1) ? "SELECT COUNT(DISTINCT " . implode(", ", $od) . ")$G" : "SELECT COUNT(*)" . ($Xd ? " FROM (SELECT 1$G GROUP BY " . implode(", ", $od) . ") x" : $G));
}

function slow_query($G)
{
    global $b, $ki, $m;
    $l = $b->database();
    $bi = $b->queryTimeout();
    $ph = $m->slowQuery($G, $bi);
    if (!$ph && support("kill") && is_object($h = connect()) && ($l == "" || $h->select_db($l))) {
        $he = $h->result(connection_id());
        echo '<script', nonce(), '>
var timeout = setTimeout(function () {
	ajax(\'', js_escape(ME), 'script=kill\', function () {
	}, \'kill=', $he, '&token=', $ki, '\');
}, ', 1000 * $bi, ');
</script>
';
    } else {
        $h = null;
    }
    ob_flush();
    flush();
    $I = @get_key_vals(($ph ? $ph : $G), $h, false);
    if ($h) {
        echo
        script("clearTimeout(timeout);");
        ob_flush();
        flush();
    }

    return $I;
}

function get_token()
{
    $vg = rand(1, 1e6);

    return ($vg ^ $_SESSION["token"]) . ":$vg";
}

function verify_token()
{
    list($ki, $vg) = explode(":", $_POST["token"]);

    return ($vg ^ $_SESSION["token"]) == $ki;
}

function lzw_decompress($Sa)
{
    $ac = 256;
    $Ta = 8;
    $mb = [];
    $Kg = 0;
    $Lg = 0;
    for ($s = 0; $s < strlen($Sa); $s++) {
        $Kg = ($Kg << 8) + ord($Sa[$s]);
        $Lg += 8;
        if ($Lg >= $Ta) {
            $Lg -= $Ta;
            $mb[] = $Kg >> $Lg;
            $Kg &= (1 << $Lg) - 1;
            $ac++;
            if ($ac >> $Ta) {
                $Ta++;
            }
        }
    }
    $Zb = range("\0", "\xFF");
    $I = "";
    foreach ($mb
             as $s => $lb) {
        $qc = $Zb[$lb];
        if (!isset($qc)) {
            $qc = $ij . $ij[0];
        }
        $I .= $qc;
        if ($s) {
            $Zb[] = $ij . $qc[0];
        }
        $ij = $qc;
    }

    return $I;
}

function on_help($tb, $mh = 0)
{
    return
        script("mixin(qsl('select, input'), {onmouseover: function (event) { helpMouseover.call(this, event, $tb, $mh) }, onmouseout: helpMouseout});", "");
}

function edit_form($a, $p, $J, $Gi)
{
    global $b, $x, $ki, $n;
    $Lh = $b->tableName(table_status1($a, true));
    page_header(($Gi ? lang(10) : lang(11)), $n, ["select" => [$a, $Lh]], $Lh);
    if ($J === false) {
        echo "<p class='error'>" . lang(12) . "\n";
    }
    echo '<form action="" method="post" enctype="multipart/form-data" id="form">
';
    if (!$p) {
        echo "<p class='error'>" . lang(13) . "\n";
    } else {
        echo "<table cellspacing='0'>" . script("qsl('table').onkeydown = editingKeydown;");
        foreach ($p
                 as $C => $o) {
            echo "<tr><th>" . $b->fieldName($o);
            $Tb = $_GET["set"][bracket_escape($C)];
            if ($Tb === null) {
                $Tb = $o["default"];
                if ($o["type"] == "bit" && preg_match("~^b'([01]*)'\$~", $Tb, $Eg)) {
                    $Tb = $Eg[1];
                }
            }
            $Y = ($J !== null ? ($J[$C] != "" && $x == "sql" && preg_match("~enum|set~", $o["type"]) ? (is_array($J[$C]) ? array_sum($J[$C]) : +$J[$C]) : $J[$C]) : (!$Gi && $o["auto_increment"] ? "" : (isset($_GET["select"]) ? false : $Tb)));
            if (!$_POST["save"] && is_string($Y)) {
                $Y = $b->editVal($Y, $o);
            }
            $r = ($_POST["save"] ? (string)$_POST["function"][$C] : ($Gi && $o["on_update"] == "CURRENT_TIMESTAMP" ? "now" : ($Y === false ? null : ($Y !== null ? '' : 'NULL'))));
            if (preg_match("~time~", $o["type"]) && $Y == "CURRENT_TIMESTAMP") {
                $Y = "";
                $r = "now";
            }
            input($o, $Y, $r);
            echo "\n";
        }
        if (!support("table")) {
            echo "<tr>" . "<th><input name='field_keys[]'>" . script("qsl('input').oninput = fieldChange;") . "<td class='function'>" . html_select("field_funs[]", $b->editFunctions(["null" => isset($_GET["select"])])) . "<td><input name='field_vals[]'>" . "\n";
        }
        echo "</table>\n";
    }
    echo "<p>\n";
    if ($p) {
        echo "<input type='submit' value='" . lang(14) . "'>\n";
        if (!isset($_GET["select"])) {
            echo "<input type='submit' name='insert' value='" . ($Gi ? lang(15) : lang(16)) . "' title='Ctrl+Shift+Enter'>\n", ($Gi ? script("qsl('input').onclick = function () { return !ajaxForm(this.form, '" . lang(17) . "...', this); };") : "");
        }
    }
    echo($Gi ? "<input type='submit' name='delete' value='" . lang(18) . "'>" . confirm() . "\n" : ($_POST || !$p ? "" : script("focus(qsa('td', qs('#form'))[1].firstChild);")));
    if (isset($_GET["select"])) {
        hidden_fields(["check" => (array)$_POST["check"], "clone" => $_POST["clone"], "all" => $_POST["all"]]);
    }
    echo '<input type="hidden" name="referer" value="', h(isset($_POST["referer"]) ? $_POST["referer"] : $_SERVER["HTTP_REFERER"]), '">
<input type="hidden" name="save" value="1">
<input type="hidden" name="token" value="', $ki, '">
</form>
';
}

if (isset($_GET["file"])) {
    if ($_SERVER["HTTP_IF_MODIFIED_SINCE"]) {
        header("HTTP/1.1 304 Not Modified");
        exit;
    }
    header("Expires: " . gmdate("D, d M Y H:i:s", time() + 365 * 24 * 60 * 60) . " GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: immutable");
    if ($_GET["file"] == "favicon.ico") {
        header("Content-Type: image/x-icon");
        echo
        lzw_decompress("\0\0\0` \0�\0\n @\0�C��\"\0`E�Q����?�tvM'�Jd�d\\�b0\0�\"��fӈ��s5����A�XPaJ�0���8�#R�T��z`�#.��c�X��Ȁ?�-\0�Im?�.�M��\0ȯ(̉��/(%�\0");
    } elseif ($_GET["file"] == "default.css") {
        header("Content-Type: text/css; charset=utf-8");
        echo
        lzw_decompress("\n1̇�ٌ�l7��B1�4vb0��fs���n2B�ѱ٘�n:�#(�b.\rDc)��a7E����l�ñ��i1̎s���-4��f�	��i7������Fé�vt2���!�r0���t~�U�'3M��W�B�'c�P�:6T\rc�A�zr_�WK�\r-�VNFS%~�c���&�\\^�r����u�ŎÞ�ً4'7k����Q��h�'g\rFB\ryT7SS�P�1=ǤcI��:�d��m>�S8L�J��t.M���	ϋ`'C����889�� �Q����2�#8А����6m����j��h�<�����9/��:�J�)ʂ�\0d>!\0Z��v�n��o(���k�7��s��>��!�R\"*nS�\0@P\"��(�#[���@g�o���zn�9k�8�n���1�I*��=�n������0�c(�;�à��!���*c��>Ύ�E7D�LJ��1����`�8(��3M��\"�39�?E�e=Ҭ�~������Ӹ7;�C����E\rd!)�a*�5ajo\0�#`�38�\0��]�e���2�	mk��e]���AZs�StZ�Z!)BR�G+�#Jv2(���c�4<�#sB�0���6YL\r�=���[�73��<�:��bx��J=	m_ ���f�l��t��I��H�3�x*���6`t6��%�U�L�eق�<�\0�AQ<P<:�#u/�:T\\>��-�xJ�͍QH\nj�L+j�z��7���`����\nk��'�N�vX>�C-T˩�����4*L�%Cj>7ߨ�ި���`���;y���q�r�3#��} :#n�\r�^�=C�Aܸ�Ǝ�s&8��K&��*0��t�S���=�[��:�\\]�E݌�/O�>^]�ø�<����gZ�V��q����� ��x\\������޺��\"J�\\î��##���D��x6��5x�������\rH�l ����b��r�7��6���j|����ۖ*�FAquvyO��WeM����D.F��:R�\$-����T!�DS`�8D�~��A`(�em�����T@O1@��X��\nLp�P�����m�yf��)	���GSEI���xC(s(a�?\$`tE�n��,�� \$a��U>,�В\$Z�kDm,G\0��\\��i��%ʹ� n��������g���b	y`��Ԇ�W� 䗗�_C��T\ni��H%�da��i�7�At�,��J�X4n����0o͹�9g\nzm�M%`�'I���О-���7:p�3p��Q�rED������b2]�PF����>e���3j\n�߰t!�?4f�tK;��\rΞи�!�o�u�?���Ph���0uIC}'~��2�v�Q���8)���7�DI�=��y&��ea�s*hɕjlA�(�\"�\\��m^i��M)��^�	|~�l��#!Y�f81RS����!���62P�C��l&���xd!�|��9�`�_OY�=��G�[E�-eL�CvT� )�@�j-5���pSg�.�G=���ZE��\$\0�цKj�U��\$���G'I�P��~�ځ� ;��hNێG%*�Rj�X[�XPf^��|��T!�*N��І�\rU��^q1V!��Uz,�I|7�7�r,���7���ľB���;�+���ߕ�A�p����^���~ؼW!3P�I8]��v�J��f�q�|,���9W�f`\0�q�Z�p}[Jdhy��N�Y|�Cy,�<s A�{e�Q���hd���Ǉ �B4;ks&�������a�������;˹}�S��J���)�=d��|���Nd��I�*8���dl�ѓ�E6~Ϩ�F����X`�M\rʞ/�%B/V�I�N&;���0�UC cT&.E+��������@�0`;���G�5��ަj'������Ɛ�Y�+��QZ-i���yv��I�5��,O|�P�]Fۏ�����\0���2�49͢���n/χ]س&��I^�=�l��qfI��= �]x1GR�&�e�7��)��'��:B�B�>a�z�-���2.����bz���#�����Uᓍ�L7-�w�t�3ɵ��e���D��\$�#���j�@�G�8� �7p���R�YC��~��:�@��EU�J��;67v]�J'���q1ϳ�El�QІi�����/��{k<��֡M�po�}��r��q�؞�c�ä�_m�w��^�u������������ln���	��_�~�G�n����{kܞ�w���\rj~�K�\0�����-����B�;����b`}�CC,���-��L��8\r,��kl�ǌ�n}-5����3u�gm��Ÿ�*�/������׏�`�`�#x�+B?#�ۏN;OR\r����\$�����k��ϙ\01\0k�\0�8��a��/t���#(&�l&���p��삅���i�M�{�zp*�-g���v��6�k�	���d�؋����A`6�lX)+d ��7 �\r�� �ځcj6��\rp�\r��\r\"oP�7�\r��\0�\0�y��P���\rQ7���Z��4Q���ڍp/�y\r��##D�;����<�g�\0fi2�)f�\\	m�Gh\r�#�n����@[ �G�\"Sqm��\r���#�(Aj��qѣ%���̑3qE��\0r�����0��я����.��Q7шW���u����� �@�H��q'vs�0�\n�+0����SG�p�O`�\r)c�#�����R=\$�ƐR\r�Gы\$R?%2C�[\0؍�~�!�\\��p�#@���O(rg%�?ra\$��)r](��&�?&�#&R�',\rqV3�\"H�m+���l�Q\"\0�4��\$r�,�=����&2;.�H@`���a����\$�_*RIS&��q��_�1�1+1������3)2�V7��2l�ڄ!1g-�2f`���,Q�7��0qg�]!q��m6����_�M7 ���7�o6Q����kp�3�g9��s� 3�6�\r�:S�9ӏ;� �\r9�-\0�Yӧ0Q�<b#<Ӂ�w/�G��>r�\r��=3��^&Q;ѣ?q�0\"�0HЙ�|���ʖS��i��@*�T�2�T#�� �\0�C��07]?��&���E��D�;:/�3�E�5��EQ�e��T\"�m����5�E;��#=4�8��*���LS�5Hr�JE TO\rԅJ��J��J���eG)8B�8�,&�G����	��+M���ɲ��^*���G��14�6�\$.\"拢�I4w!\$L �8b�A2�L�'M?MF�\$�,����Nr��/4�BJ�¨");
    } elseif ($_GET["file"] == "functions.js") {
        header("Content-Type: text/javascript; charset=utf-8");
        echo
        lzw_decompress("f:��gCI��\n8��3)��7���81��x:\nOg#)��r7\n\"��`�|2�gSi�H)N�S��\r��\"0��@�)�`(\$s6O!��V/=��' T4�=��iS��6IO��er�x�9�*ź��n3�\rщv�C��`���2G%�Y�����1��f���Ȃl��1�\ny�*pC\r\$�n�T��3=\\�r9O\"�	��l<�\r�\\��I,�s\nA��eh+M�!�q0��f�`(�N{c��+w���Y��p٧3�3��+I��j�����k��n�q���zi#^r�����3���[��o;��(��6�#�Ґ��\":cz>ߣC2v�CX�<�P��c*5\n���/�P97�|F��c0�����!���!���!��\nZ%�ć#CH�!��r8�\$���,�Rܔ2���^0��@�2��(�88P/��݄�\\�\$La\\�;c�H��HX���\nʃt���8A<�sZ�*�;I��3��@�2<���!A8G<�j�-K�({*\r��a1���N4Tc\"\\�!=1^���M9O�:�;j��\r�X��L#H�7�#Tݪ/-���p�;�B \n�2!���t]apΎ��\0R�C�v�M�I,\r���\0Hv��?kT�4����uٱ�;&���+&���\r�X���bu4ݡi88�2B�/⃖4���N8A�A)52������2��s�8�5���p�WC@�:�t�㾴�e��h\"#8_��cp^��I]OH��:zd�3g�(���Ök��\\6����2�ږ��i��7���]\r�xO�n�p�<��p�Q�U�n��|@���#G3��8bA��6�2�67%#�\\8\r��2�c\r�ݟk��.(�	��-�J;��� ��L�� ���W��㧓ѥɤ����n��ҧ���M��9ZНs]�z����y^[��4-�U\0ta��62^��.`���.C�j�[ᄠ% Q\0`d�M8�����\$O0`4���\n\0a\rA�<�@����\r!�:�BA�9�?h>�Ǻ��~̌�6Ȉh�=�-�A7X��և\\�\r��Q<蚧q�'!XΓ2�T �!�D\r��,K�\"�%�H�qR\r�̠��C =�������<c�\n#<�5�M� �E��y�������o\"�cJKL2�&��eR��W�AΐTw�ё;�J���\\`)5��ޜB�qhT3��R	�'\r+\":�8��tV�A�+]��S72��Y�F��Z85�c,���J��/+S�nBpoW�d��\"�Q��a�ZKp�ާy\$�����4�I�@L'@�xC�df�~}Q*�ҺA��Q�\"B�*2\0�.��kF�\"\r��� �o�\\�Ԣ���VijY��M��O�\$��2�ThH����0XH�5~kL���T*:~P��2�t���B\0�Y������j�vD�s.�9�s��̤�P�*x���b�o����P�\$�W/�*��z';��\$�*����d�m�Ã�'b\r�n%��47W�-�������K���@<�g�èbB��[7�\\�|�VdR��6leQ�`(Ԣ,�d��8\r�]S:?�1�`��Y�`�A�ғ%��ZkQ�sM�*���{`�J*�w��ӊ>�վ�D���>�eӾ�\"�t+po������W\$����Q�@��3t`����-k7g��]��l��E��^dW>nv�t�lzPH��FvW�V\n�h;��B�D�س/�:J��\\�+ %�����]��ъ��wa�ݫ���=��X��N�/��w�J�_[�t)5���QR2l�-:�Y9�&l R;�u#S	� ht�k�E!l���>SH��X<,��O�YyЃ%L�]\0�	��^�dw�3�,Sc�Qt�e=�M:4���2]��P�T�s��n:��u>�/�d�� ��a�'%����qҨ&@֐���H�G�@w8p����΁�Z\n��{�[�t2���a��>	�w�J�^+u~�o��µXkզBZk˱�X=��0>�t��lŃ)Wb�ܦ��'�A�,��m�Y�,�A���e��#V��+�n1I����E�+[����[��-R�mK9��~���L�-3O���`_0s���L;�����]�6��|��h�V�T:��ޞerM��a�\$~e�9�>����Д�\r��\\���J1Ú���%�=0{�	����|ޗtڼ�=���Q�|\0?��[g@u?ɝ|��4�*��c-7�4\ri'^���n;�������(���{K�h�nf���Zϝ}l�����]\r��pJ>�,gp{�;�\0��u)��s�N�'����H��C9M5��*��`�k�㬎����AhY��*����jJ�ǅPN+^� D�*��À���D��P���LQ`O&��\0�}�\$���6�Zn>��0� �e��\n��	�trp!�hV�'Py�^�*|r%|\nr\r#���@w����T.Rv�8�j�\nmB���p�� �Y0�Ϣ�m\0�@P\r8�Y\rG��d�	�QG�P%E�/@]\r���{\0�Q����bR M\rF��|��%0SDr�����f/����\":�mo�ރ�%�@�3H�x\0�l\0���	��W����\n�8\r\0}�@�D��`#�t��.�jEoDrǢlb����t�f4�0���%�0���k�z2\r� �W@�%\r\n~1��X����D2!��O�*���{0<E��k*m�0ı���|\r\n�^i��� ��!.�r � ��������f��Ĭ��+:��ŋJ�B5\$L���P���LĂ�� Z@����`^P�L%5%jp�H�W��on��kA#&���8��<K6�/����̏������XWe+&�%���c&rj��'%�x�����nK�2�2ֶ�l��*�.�r��΢���*�\r+jp�Bg�{ ���0�%1(���Z�`Q#�Ԏ�n*h��v�B����\\F\n�W�r f\$�93�G4%d�b�:JZ!�,��_��f%2��6s*F���Һ�EQ�q~��`ts�Ҁ���(�`�\r���#�R����R�r��X��:R�)�A*3�\$l�*ν:\"Xl��tbK�-��O>R�-�d��=��\$S�\$�2��}7Sf��[�}\"@�]�[6S|SE_>�q-�@z`�;�0��ƻ��C�*��[���{D��jC\nf�s�P�6'���ȕ QE���N\\%r�o�7o�G+dW4A*��#TqE�f��%�D�Z�3��2.��Rk��z@��@�E�D�`C�V!C��ŕ\0���I�)38��M3�@�3L��ZB�1F@L�h~G�1M���6��4�Xє�}ƞf�ˢIN��34��X�Btd�8\nbtN��Qb;�ܑD��L�\0��\"\n����V��6��]U�cVf���D`�M�6�O4�4sJ��55�5�\\x	�<5[F�ߵy7m�)@SV��Ğ#�x��8 ոы��`�\\`�-�v2���p���+v���U��L�xY.����\0005(�@��ⰵ[U@#�VJuX4�u_�\"JO(Dt�_	5s�^���������5�^�^V�I��\rg&]��\r\"ZCI�6��#��\r��ܓ��]7���q�0��6}o���`u��ab(�X�D�f�M�N)�V�UUF�о��=jSWi�\"\\B1Ğ�E0� �amP��&<�O_�L���.c�1Z*��R\$�h���mv�[v>ݭ�p����(��0����cP�om\0R��p�&�w+KQ�s6�}5[s�J���2��/���O �V*)�R�.Du33�F\r�;��v4���H�	_!��2��k����+��%�:�_,�eo��F��AJ�O�\"%�\n�k5`z %|�%�Ϋg|��}l�v2n7�~\0�	�YRH��@��r��xN-Jp\0���f#��@ˀmv�x��\r���2WMO/�\nD��7�}2���VW�W��wɀ7����H�k���]�\$�Mz\\�e�.f�RZ�a�B���Qd�KZ��vt���w4�\0�Z@�	��Bc;�b��>�B�	3m�n\n�o��J3��k�(܍���\"�yG\$:\r�ņ�ݎ��G6�ɲJ��y��Q�\\Q��if�����(�m)/r�\$�J�/�H�]*���g�ZOD�Ѭ��]1�g22������f�=HT��]N�&���M\0�[8x�ȮE��8&L�Vm�v����j�ט�F��\\��	���&s�@Q� \\\"�b��	��\rBs�Iw�	�Yɜ�N �7�C/&٫`�\n\n��[k���*A���T�V*UZtz{�.��y�S���#�3�ipzW@yC\nKT��1@|�z#���_CJz(B�,V�(K�_��dO���P�@X��t�Ѕ��c;�WZzW�_٠�\0ފ�CF�xR �	�\n������P�A��&������,�pfV|@N�\"�\$�[�i����������Z�\0Zd\\\"�|�W`��]��tz�o\$�\0[����u�e���ə�bhU-��,�r �Lk8��֫�V&�al����d��2;	�'-��Jyu��a���\0����a��{s�[9V\0��F��R �VB0S;D�>L4�&�ZHO1�\0�wg��S�tK��R�z���i��+�3�w��z�X�]�(G\$����D+�tչ�(#����oc�:	��Y6�\0��&��	@�	���)��!����w���# t�x�ND�����)��C��FZ�p��a��*F�b�	��ͼ����ģ�����Si/S�!��z�UH*�4����0�K�-�/���-k`�n�Li�J�~�w�Jn��\"�`�=��V�3Oį8t�>��vo��E.��Rz`��p�P���E\\��ɧ�3L�l�ѥs]T���oV��\n��	*�\r�@7)��D�m�0W�5Ӏ��ǰ�w��b���|	��JV����\"�ur\r�&N0N�B�d��d�8�D��_ͫ�^T��H#]�d�+�v�~�U,�PR%�����x���fA��C��m����͸����c��yŜD)���uH���p�p�^u\0�����}�{ѡ�\rg�s�QM�Y�2j�\r�|0\0X��@q���I`��5F�6�N��V@ӔsE�p���#\r�P�T��DeW�ؼ񛭁��z!û�:�DMV(��~X���9�\0�@���40N�ܽ~�Q�[T���e�qSv\"�\"h�\0R-�hZ�d����F5�P��`�9�D&xs9W֗5Er@o�wkb�1��PO-O�OxlH�D6/ֿ�m�ޠ��3�7T��K�~54�	�p#�I�>YIN\\5���NӃ����M��pr&�G�xM�sq����.F���8�Cs�� h�e5������*�b�)Sڪ��̭�e�0�-X� {�5|�i�֢a��ȕ6z�޽��/Y���ێM� ƃ� �\nR*8r o� @7�8Bf�z�K�r���A\$˰	p�\0?���d�k�|45}�A����ɶ�W��J�2k Gi\0\"����d���8�\0�>m��� `8�w�7�o4�cGh��Q�(퀨�8@\$<\0p��0���L�eX+�Ja�{�B��h��8�Cy���P2��Ӯ�*�EH�2���DqS�ۘ�p�0�I���k�`��S�\n�:��B�7����{-����`����6�A�W�ܖ\r�p�W#���?���{\0������cD��[<����f�--�pԌ�*B�]�nW��^��R70\r�+N�GN�\$(\0�#+y�@�@iD(8@\r�h��H�He����zz�{1���h��W1F�Who&aɜ�d6���jw�������`h�{v`RE�\nj���`�ܷ����*���ʸ}�Y��	\rY�H�6�#\0�廆��a�� Q�HEl4�d���p��#�������o�br+_)\r`��!�|dQ�>��=Qʡ��ζ�EOB'�>�P��Ӷ� A\rnK�i�� 	�����	�%<	�o;�S�@�!	�x��:���A�+\\1d\$�jO��7�%�	�/����gu�z*�G�H�5\"8��,�]raq���/�h��#����\$ /tn��8y��-�O���H�b���<�Z�!���1��`�.(uo����|`GːS��BaM	ڂ9ƞ�D@���1�B�tD��ʡ@?o�(H��qC��8E�TcncR��6�N%�rHj��2G\0�a��q �r��z9b>(P��x��<��)�x#�8�誹t���h�2v��Wo2U���t��+=�l#���j�D�	0����&R�c�\$�*̑-Z`��\r��;�|A�p�=1�	1����ƈ�bEv(^�X�P2=\0}�W���G�<���G�����R�#P�Hܮr9	��Y��!�LB���4�NC�Z��IC���MLm��,�f@eY�x�BS(�+��<4Y�)-�\r�z?\$���\"\"�� 6�E�\r)z���@ȑ��r����*��J�윋��%\$�e�J���\0A�\$ڰ/5��B0S���x��I�Q)�<��4YS�&�{��b�+IG=>�\r�PY`Z�D�`��U����F1���4d8X(����C%�`�㜭0�I\$�7W�pǁ,��Ac���&Ԍ�p\$�:�>]�.�VY��\$p� ��]��`�;��e�\0�0�\n��K+�@DL�S��r(on�M\0@9��%�\"�WS�\"���� 䥙�ٍ�ػj�_J-��rʜ���5�\\�2�5>Ze\"0��%9y��^�WMax&a)D�L���2Q����t?�=,�/o�f�3I�J�\$\r;���7�}�\r�W�@�Ұ�M|\r�Y���]5���\\*s:��FV!���kن�R���L3L�	��52�M�sb�\$����7�\0l�y���&� 9�|m!��0J��4��TSd���G���nK�V:l�D'/��:Zs��\n��y�%��i����,@ҲL��j1<��3Ĩ�D2/;��'Pݻ����`���qKȰ�f�I�L� Dݬ�4�3 ��OH�J�	q�&�����X��!��r)F�Xx���^QwOP��h��՞-_�>�a����(	��x%��K�b�<�E�j7�������hHt�`�.r�P���x��\"{\0006CVQE�&��>�ޅ�w����e'?B�9x�>:\"�73���xT\0e����j	��[t�Ҝ\"�(\\K�e�z�r����e> ���\0002�hʇ��X�a<�JtU�z`�達?��#�����2-��4hFY|C��\"M�yƔKd ���E�7���+(U�ʖX�� /D���)�\"����بމjoh�Fz4�t���D׌�G��RZ�ć�ȿ\0�FV4Q�6v�b�i=G�;Ϭ�k�d+\n>�E��\0�2f{����!J��Q�J�ؘ9��(2�#\\Z��,��Qܥ�3?8`�	bwR6��\n*�㋀�ƒ�(t��L*�S�d�\0x�)�(�*�wH]7O�N�v(Гdg�q	\nLp��L�N��H@�1����M �		n��z���e4!!	��'槝-t���AQP���L,����7��\\�i����^�\$�,�|�Z��(S9���\n* +��T�D�z?(T�>��L��æ��R����\$�zдi̼W�ͨ�Ds�{)�@�����	v�P��g�qIVҨ����\n )�!�8|\$pZ�*�!7A����N��j�NW����U���Q���)�eF�UA�S�x\0[N���2���X :S�T�~�S*T4	�3��]9�F���]:�KUg;��*Ay�a��1j|8Ϋ����I�MR��Vh7uU���r,�h�%<q�R@N9�ާ�k�	�B|�����8��r������DР@\"�ɋ�z\r������O�_���Q�\0\0���|�]�f�\nz�����UeH�Ą/k+�TF?��*03�!�\0��I���t	f\0(S�U��ZA�F��1\0��k�]��WZN�Q��܂���%��x1���'��!-,�Ƕvzg��#�Gh�;f�PH�9Bj�u�\n�A�VR����1K+�MN!��Sμ��Y��vdZ\\,���g٨�����\"}W��Yɵ�t�P��g�,�����	\0b�-�hB/@�̎�/�M��J���Y\0����)\n��I�?v�	��Ȕ1��\$�(�w\r+�n ��s�s�QfQ�O�P�.D���bV\0-�J<�i;[���=#���n,j?)�\"���lYL.����A::������BxOF7����`���d��}�}=�i)@к��\$ q˷(y%��huzb2�3Ƨ��.�-h�oO����\0`���VZ��&y�t9C���鋭Z��ґ�Z!�X�U����.k��V#8�G�}�Q���u8cΫt�bE>�v��{@{QP]<�ary��j\\��\$j�x�nc6k�;qs�T���K�����jJ���n\\C��{���`g�6�5�Rk�t����s�|@�_0΅5:B�3����rѡ�&�㴸�\0����&�׈�����ԡ��SXʕ�G�m�ʶWr,j�q\0\$޺sW�P�.A\n4�9(u�.���l�V�Ju�Ԍ�+�A�uC�>hl6��2���G�e���N��n�=�'���~��Þ��PҀ�%0z�u��r�\0��9uE�s\"���\\�ט���^���(3ՑS%<+�9��Ծ����\0���~'̞�֓<+�,i�:��@��N���\$�o������� �]������Z�!��]�n,��x��>_�f��W\0006��%�}I�\nh߀w�����ǃ -��H@_�Vi�����{���R��^�۔}5�b,!5���H��p/��k<��<�jh|i��k��hLv݄\n�`�[���WC6��z\n�g��r��u=��!zCţ���e#��nj��\0`^;=E�*@�y�% ��LQe���2�A�1,��C�ix�t����G�]q�O(����\n�V9dr�D'5@x\$�r6��;\"ǣ���7�\0M0ņH_#�c�pn>��<aa�q@g�2��lm-��������8��?8��7p����>��ji���N�\$#E/�0��s\n�B\r�*��z��oyn[Ι�� 6�a����g8�qC��⼜�I�rNF�ȫ�1��70�����/i(�B�0����Z��(��+S�J�,��91/Y+jxӱF���A��k�f�Jee\r�Cͳrz�m���h@9�O�� ؝��GK�Ad���OH���=���<&`��K�PA�!WO;-�X�L��m�Kz�7-e[u��p�q���o/�`�C����KX�f�i��Y7=�M�/�F�R�۔T�d��Y\"=`�1�k�1Տh�\r���f@N��z�(@����	h�\0�����I�}PJKr���pR`x������fo���(A��[��19�(&jo<��I@p	@�����,y�	nIs�^Ўѫ:Y��vc���؏9q.C��8�bW��V?��҅�9�\$u�@5#S(4Y���K���6�!��N6<��|v1��3ʊ:��!����`��M��l����f`�Z��J=��GX�Y)_l�А�T�)P��`�%��:�!Z\"lYS�Uؤ(��Y1Z�니rv)F`�K~=Y>���S���c����!l���D����BrF\$��RA:�\\�P�4�V�R6<�O�S�_BCS+����'V��2T#Lc�F�NBD%�G�W�nR�S����I��\n'k�0���O��Ў����8rݯAS�?��xm��yv���a�b��Ͱ�,��ЅA������]pJ\\\\�Xi���Eu��B)���Z@Ώ \"��gg0{��n��'APR��٨v�~�0R�w쀱\"�������H�J���Ζ�\\�\r}i?�Ғ:��2���g��{I�3)��B��͙Z�s��`.�#2�vt�X�IGU>`)�%���(|�f<Κ_�ޯ���_G�<��_ ˟������[:�6G8��l�#J(��JC���`���wF�w\"b�!,��!�r�@�K(���\n@AsV��S�ֹ�4�_\ns٠eڋj��)&�3�{��k���Q���G�c��X^�L{�C\n�m����A��D��1O?(��(�����2\"UL��+#o��@���X�\0�٭���^n_p�eQ˙X}%��*��e�m�{�GN��Xl�q�]R\\Z�v!�) ���xd΀,�cK��鮇�m���I~����K�{+��Gݥ�=@Q��,1!aEOc��#6<u��rB�\n�Ȳ��dH�t����	�{C�<x3���H��1��K�wB�\0��u����'ӆQ�^���򕥂�i�rRv�Vɷ�lS�.O)����[��xS�t���c)���k�B��+��v���B��w�.�wC���2���2d�.H��p+a\\H��[�\$}nNN7��H�.�S\r�ȒT���w�	*H�g\\��\$�,�:KBOx��>����5����Ӷ����u2��n��`��Yq�D���xwMB�n�2>���G�ڄ����YaK�w(2`����w����1m�-:�&LD8�U��8l��\\<���	��z�a����:,��K'�%7:���M����U[���*;K���j�;/wG���\n���^�eV'��,��;��B6�G�1��OKW����(i�X\np�Cکc6�^��㷀=�^ûcQ��Rp`\$	�D(\0D�>{�ET�c��I\r{���\$o�R	�ZZ�4*��??�+j���n��Q`����X�3�	\$���M�\n׉w�\"d�W���~@�'�I�᭫�0+-��w����y�6�vȽ'�Ԇ:Y)Y0\0�*)?'��Ǟv����fI�\n��z�9�.�b��!�c�E�[��F麙ks�}��Bv�g�5�V���,)J\$��j�Z�J�\$�Y��ח9�\0�\n����.^J��ڋ�b��mI0:g��������˗ATP�I�]~!��;D�����	�z��<P�Q>�m���`��?%Y��T\n\0D\0�\0'���H@0`�<׭�10�(�m�-��ɞ7A\0�~�~ꁡĤ?t�hє.w�%)0	#c����\"�c����jfW��\0\0p��C���kC��8��85+i:��[�8�b��l�[\"����5S�y\0�����*�Q�6V�s�9��7!�;\"��c�)�O�Q,��Ա��\r�7�,*�0�aQ�u?�_C|�������R(o(��<j(��Tv��\r|_\"�3��m��S7D�!׸�h�|���(�&�@:�	\"-ގ��&Mu;�,�bк=p�>A6ɭ���7���- WW9�O,�o'�v2�<�3\0���h��@`� 3TX�Ϛ|�\"FC_��~x����`��'f�Q-4�����/�`'���=A�\$>��`P��_G(���E���&/J�I�v�'�m餧zpޞFo�	�/[��i�؋�G*���y�(�<��7q�Y�.�眪��B���\r�l�r\nUnƧ��T>�������	�Q���_�|����K��8�ډ�e��_��xz�x�L���p14��d����U#4t�K���\$�!����p�w����Zx��_����i5T?}��C�{�����h/Gzj\$.B�Ҩ�=#�Ϗ|��*����I��w/��a�x`*��*���]����>a?'}FJS���ԖA0��'�����ʟ�0:63���л��n'��U/�r�|=slb0�\0W�rB�ʤ���@T��~\$����H�����	��D\\���-���(��ᩖB�M���z+�%�(��i��㹃�I���5/�.y/���\$�{Q}p�ܻdI�\\�Վ�B�\0V0�B�9�{T\$n�8\$Z�e�Pĳ���%9�&���V��b�x}g\"%h���*ٸvOw�˾�/�o�L,���=��V��5Bg� ϶�3��>�~�`\nxi�\"��v@�����nף�ϳyac�G�'%[��4`n��47!5�ހr����ɉ��>z�(Y�t��0���V���P�ZXT`2�~Cl���[o�n�t8jB\0d�\0000��V��g�����@V!�h\0006d<��=[�W�����f�@pb��a��ټ�s;���G<�~a�?�N�L����\"(���?�%�x#�7�|S��O�Ɠ)�B4��+��*�!��)6#�+?'���(X�����JO\0��");
    } elseif ($_GET["file"] == "jush.js") {
        header("Content-Type: text/javascript; charset=utf-8");
        echo
        lzw_decompress("v0��F����==��FS	��_6MƳ���r:�E�CI��o:�C��Xc��\r�؄J(:=�E���a28�x�?�'�i�SANN���xs�NB��Vl0���S	��Ul�(D|҄��P��>�E�㩶yHch��-3Eb�� �b��pE�p�9.����~\n�?Kb�iw|�`��d.�x8EN��!��2��3���\r���Y���y6GFmY�8o7\n\r�0��\0�Dbc�!�Q7Шd8���~��N)�Eг`�Ns��`�S)�O���/�<�x�9�o�����3n��2�!r�:;�+�9�CȨ���\n<�`��b�\\�?�`�4\r#`�<�Be�B#�N ��\r.D`��j�4���p�ar��㢺�>�8�\$�c��1�c���c����{n7����A�N�RLi\r1���!�(�j´�+��62�X�8+����.\r����!x���h�'��6S�\0R����O�\n��1(W0���7q��:N�E:68n+��մ5_(�s�\r��/m�6P�@�EQ���9\n�V-���\"�.:�J��8we�q�|؇�X�]��Y X�e�zW�� �7��Z1��hQf��u�j�4Z{p\\AU�J<��k��@�ɍ��@�}&���L7U�wuYh��2��@�u� P�7�A�h����3Û��XEͅZ�]�l�@Mplv�)� ��HW���y>�Y�-�Y��/�������hC�[*��F�#~�!�`�\r#0P�C˝�f������\\���^�%B<�\\�f�ޱ�����&/�O��L\\jF��jZ�1�\\:ƴ>�N��XaF�A�������f�h{\"s\n�64������?�8�^p�\"띰�ȸ\\�e(�P�N��q[g��r�&�}Ph���W��*��r_s�P�h���\n���om������#���.�\0@�pdW �\$Һ�Q۽Tl0� ��HdH�)��ۏ��)P���H�g��U����B�e\r�t:��\0)\"�t�,�����[�(D�O\nR8!�Ƭ֚��lA�V��4�h��Sq<��@}���gK�]���]�=90��'����wA<����a�~��W��D|A���2�X�U2��yŊ��=�p)�\0P	�s��n�3�r�f\0�F���v��G��I@�%���+��_I`����\r.��N���KI�[�ʖSJ���aUf�Sz���M��%��\"Q|9��Bc�a�q\0�8�#�<a��:z1Uf��>�Z�l������e5#U@iUG��n�%Ұs���;gxL�pP�?B��Q�\\�b��龒Q�=7�:��ݡQ�\r:�t�:y(� �\n�d)���\n�X;����CaA�\r���P�GH�!���@�9\n\nAl~H���V\ns��ի�Ư�bBr���������3�\r�P�%�ф\r}b/�Α\$�5�P�C�\"w�B_��U�gAt��夅�^Q��U���j���Bvh졄4�)��+�)<�j^�<L��4U*���Bg�����*n�ʖ�-����	9O\$��طzyM�3�\\9���.o�����E(i������7	tߚ�-&�\nj!\r��y�y�D1g���]��yR�7\"������~����)TZ0E9M�YZtXe!�f�@�{Ȭyl	8�;���R{��8�Į�e�+UL�'�F�1���8PE5-	�_!�7��[2�J��;�HR��ǹ�8p痲݇@��0,ծpsK0\r�4��\$sJ���4�DZ��I��'\$cL�R��MpY&����i�z3G�zҚJ%��P�-��[�/x�T�{p��z�C�v���:�V'�\\��KJa��M�&���Ӿ\"�e�o^Q+h^��iT��1�OR�l�,5[ݘ\$��)��jLƁU`�S�`Z^�|��r�=��n登��TU	1Hyk��t+\0v�D�\r	<��ƙ��jG���t�*3%k�YܲT*�|\"C��lhE�(�\r�8r��{��0����D�_��.6и�;����rBj�O'ۜ���>\$��`^6��9�#����4X��mh8:��c��0��;�/ԉ����;�\\'(��t�'+�����̷�^�]��N�v��#�,�v���O�i�ϖ�>��<S�A\\�\\��!�3*tl`�u�\0p'�7�P�9�bs�{�v�{��7�\"{��r�a�(�^��E����g��/���U�9g���/��`�\nL\n�)���(A�a�\" ���	�&�P��@O\n師0�(M&�FJ'�! �0�<�H�������*�|��*�OZ�m*n/b�/�������.��o\0��dn�)����i�:R���P2�m�\0/v�OX���Fʳψ���\"�����0�0�����0b��gj��\$�n�0}�	�@�=MƂ0n�P�/p�ot������.�̽�g\0�)o�\n0���\rF����b�i��o}\n�̯�	NQ�'�x�Fa�J���L������\r��\r����0��'��d	oep��4D��ʐ�q(~�� �\r�E��pr�QVFH�l��Kj���N&�j!�H`�_bh\r1���n!�Ɏ�z�����\\��\r���`V_k��\"\\ׂ'V��\0ʾ`AC������V�`\r%�����\r����k@N����B�횙� �!�\n�\0Z�6�\$d��,%�%la�H�\n�#�S\$!\$@��2���I\$r�{!��J�2H�ZM\\��hb,�'||cj~g�r�`�ļ�\$���+�A1�E���� <�L��\$�Y%-FD��d�L焳��\n@�bVf�;2_(��L�п��<%@ڜ,\"�d��N�er�\0�`��Z��4�'ld9-�#`��Ŗ����j6�ƣ�v���N�͐f��@܆�&�B\$�(�Z&���278I ��P\rk\\���2`�\rdLb@E��2`P( B'�����0�&��{���:��dB�1�^؉*\r\0c<K�|�5sZ�`���O3�5=@�5�C>@�W*	=\0N<g�6s67Sm7u?	{<&L�.3~D��\rŚ�x��),r�in�/��O\0o{0k�]3>m��1\0�I@�9T34+ԙ@e�GFMC�\rE3�Etm!�#1�D @�H(��n ��<g,V`R]@����3Cr7s~�GI�i@\0v��5\rV�'������P��\r�\$<b�%(�Dd��PW����b�fO �x\0�} ��lb�&�vj4�LS��ִԶ5&dsF M�4��\".H�M0�1uL�\"��/J`�{�����xǐYu*\"U.I53Q�3Q��J��g��5�s���&jь��u�٭ЪGQMTmGB�tl-c�*��\r��Z7���*hs/RUV����B�Nˈ�����Ԋ�i�Lk�.���t�龩�rYi���-S��3�\\�T�OM^�G>�ZQj���\"���i��MsS�S\$Ib	f���u����:�SB|i��Y¦��8	v�#�D�4`��.��^�H�M�_ռ�u��U�z`Z�J	e��@Ce��a�\"m�b�6ԯJR���T�?ԣXMZ��І��p����Qv�j�jV�{���C�\r��7�Tʞ� ��5{P��]�\r�?Q�AA������2񾠓V)Ji��-N99f�l Jm��;u�@�<F�Ѡ�e�j��Ħ�I�<+CW@�����Z�l�1�<2�iF�7`KG�~L&+N��YtWH飑w	����l��s'g��q+L�zbiz���Ţ�.Њ�zW�� �zd�W����(�y)v�E4,\0�\"d��\$B�{��!)1U�5bp#�}m=��@�w�	P\0�\r�����`O|���	�ɍ����Y��JՂ�E��Ou�_�\n`F`�}M�.#1��f�*�ա��  �z�uc���� xf�8kZR�s2ʂ-���Z2�+�ʷ�(�sU�cD�ѷ���X!��u�&-vP�ر\0'L�X �L����o	��>�Վ�\r@�P�\rxF��E��ȭ�%����=5N֜��?�7�N�Å�w�`�hX�98 �����q��z��d%6̂t�/������L��l��,�Ka�N~�����,�'�ǀM\rf9�w��!x��x[�ϑ�G�8;�xA��-I�&5\$�D\$���%��xѬ���´���]����&o�-3�9�L��z���y6�;u�zZ ��8�_�ɐx\0D?�X7����y�OY.#3�8��ǀ�e�Q�=؀*��G�wm ���Y�����]YOY�F���)�z#\$e��)�/�z?�z;����^��F�Zg�����������`^�e����#�������?��e��M��3u�偃0�>�\"?��@חXv�\"������*Ԣ\r6v~��OV~�&ר�^g���đٞ�'��f6:-Z~��O6;zx��;&!�+{9M�ٳd� \r,9���W��ݭ:�\r�ٜ��@睂+��]��-�[g��ۇ[s�[i��i�q��y��x�+�|7�{7�|w�}����E��W��Wk�|J؁��xm��q xwyj���#��e��(�������ߞþ��� {��ڏ�y���M���@��ɂ��Y�(g͚-��������J(���@�;�y�#S���Y��p@�%�s��o�9;�������+��	�;����ZNٯº��� k�V��u�[�x��|q��ON?���	�`u��6�|�|X����س|O�x!�:���ϗY]�����c���\r�h�9n�������8'������\rS.1��USȸ��X��+��z]ɵ��?����C�\r��\\����\$�`��)U�|ˤ|Ѩx'՜����<�̙e�|�ͳ����L���M�y�(ۧ�l�к�O]{Ѿ�FD���}�yu��Ē�,XL\\�x��;U��Wt�v��\\OxWJ9Ȓ�R5�WiMi[�K��f(\0�dĚ�迩�\r�M����7�;��������6�KʦI�\r���xv\r�V3���ɱ.��R������|��^2�^0߾\$�Q��[�D��ܣ�>1'^X~t�1\"6L���+��A��e�����I��~����@����pM>�m<��SK��-H���T76�SMfg�=��GPʰ�P�\r��>�����2Sb\$�C[���(�)��%Q#G`u��Gwp\rk�Ke�zhj��zi(��rO�������T=�7���~�4\"ef�~�d���V�Z���U�-�b'V�J�Z7���)T��8.<�RM�\$�����'�by�\n5����_��w����U�`ei޿J�b�g�u�S��?��`���+��� M�g�7`���\0�_�-���_��?�F�\0����X���[��J�8&~D#��{P���4ܗ��\"�\0��������@ғ��\0F ?*��^��w�О:���u��3xK�^�w���߯�y[Ԟ(���#�/zr_�g��?�\0?�1wMR&M���?�St�T]ݴG�:I����)��B�� v����1�<�t��6�:�W{���x:=��ޚ��:�!!\0x�����q&��0}z\"]��o�z���j�w�����6��J�P۞[\\ }��`S�\0�qHM�/7B��P���]FT��8S5�/I�\r�\n ��O�0aQ\n�>�2�j�;=ڬ�dA=�p�VL)X�\n¦`e\$�TƦQJ����lJ����y�I�	�:����B�bP���Z��n����U;>_�\n	�����`��uM򌂂�֍m����Lw�B\0\\b8�M��[z��&�1�\0�	�\r�T������+\\�3�Plb4-)%Wd#\n��r��MX\"ϡ�(Ei11(b`@f����S���j�D��bf�}�r����D�R1���b��A��Iy\"�Wv��gC�I�J8z\"P\\i�\\m~ZR��v�1ZB5I��i@x����-�uM\njK�U�h\$o��JϤ!�L\"#p7\0� P�\0�D�\$	�GK4e��\$�\nG�?�3�EAJF4�Ip\0��F�4��<f@� %q�<k�w��	�LOp\0�x��(	�G>�@�����9\0T����GB7�-�����G:<Q��#���Ǵ�1�&tz��0*J=�'�J>���8q��Х���	�O��X�F��Q�,����\"9��p�*�66A'�,y��IF�R��T���\"��H�R�!�j#kyF���e��z�����G\0�p��aJ`C�i�@�T�|\n�Ix�K\"��*��Tk\$c��ƔaAh��!�\"�E\0O�d�Sx�\0T	�\0���!F�\n�U�|�#S&		IvL\"����\$h���EA�N\$�%%�/\nP�1���{��) <���L���-R1��6���<�@O*\0J@q��Ԫ#�@ǵ0\$t�|�]�`��ĊA]���Pᑀ�C�p\\pҤ\0���7���@9�b�m�r�o�C+�]�Jr�f��\r�)d�����^h�I\\�. g��>���8���'�H�f�rJ�[r�o���.�v���#�#yR�+�y��^����F\0᱁�]!ɕ�ޔ++�_�,�\0<@�M-�2W���R,c���e2�*@\0�P ��c�a0�\\P���O���`I_2Qs\$�w��=:�z\0)�`�h�������\nJ@@ʫ�\0�� 6qT��4J%�N-�m����.ɋ%*cn��N�6\"\r͑�����f�A���p�MۀI7\0�M�>lO�4�S	7�c���\"�ߧ\0�6�ps�����y.��	���RK��PAo1F�tI�b*��<���@�7�˂p,�0N��:��N�m�,�xO%�!��v����gz(�M���I��	��~y���h\0U:��OZyA8�<2����us�~l���E�O�0��0]'�>��ɍ�:���;�/��w�����'~3GΖ~ӭ����c.	���vT\0c�t'�;P�\$�\$����-�s��e|�!�@d�Obw��c��'�@`P\"x����0O�5�/|�U{:b�R\"�0�шk���`BD�\nk�P��c��4�^ p6S`��\$�f;�7�?ls��߆gD�'4Xja	A��E%�	86b�:qr\r�]C8�c�F\n'ьf_9�%(��*�~��iS����@(85�T��[��Jڍ4�I�l=��Q�\$d��h�@D	-��!�_]��H�Ɗ�k6:���\\M-����\r�FJ>\n.��q�eG�5QZ����' ɢ���ہ0��zP��#������r���t����ˎ��<Q��T��3�D\\����pOE�%)77�Wt�[��@����\$F)�5qG0�-�W�v�`�*)Rr��=9qE*K\$g	��A!�PjBT:�K���!��H� R0?�6�yA)B@:Q�8B+J�5U]`�Ҭ��:���*%Ip9�̀�`KcQ�Q.B��Ltb��yJ�E�T��7���Am�䢕Ku:��Sji� 5.q%LiF��Tr��i��K�Ҩz�55T%U��U�IՂ���Y\"\nS�m���x��Ch�NZ�UZ���( B��\$Y�V��u@蔻����|	�\$\0�\0�oZw2Ҁx2���k\$�*I6I�n�����I,��QU4�\n��).�Q���aI�]����L�h\"�f���>�:Z�>L�`n�ض��7�VLZu��e��X����B���B�����Z`;���J�]�����S8��f \nڶ�#\$�jM(��ޡ����a�G��+A�!�xL/\0)	C�\n�W@�4�����۩� ��RZ����=���8�`�8~�h��P ��\r�	���D-FyX�+�f�QSj+X�|��9-��s�x�����+�V�cbp쿔o6H�q�����@.��l�8g�YM��WMP��U��YL�3Pa�H2�9��:�a�`��d\0�&�Y��Y0٘��S�-��%;/�T�BS�P�%f������@�F�(�֍*�q +[�Z:�QY\0޴�JUY֓/���pkzȈ�,�𪇃j�ꀥW�״e�J�F��VBI�\r��pF�Nقֶ�*ը�3k�0�D�{����`q��ҲBq�e�D�c���V�E���n����FG�E�>j�����0g�a|�Sh�7u�݄�\$���;a��7&��R[WX���(q�#���P���ז�c8!�H���VX�Ď�j��Z������Q,DUaQ�X0��ը���Gb��l�B�t9-oZ���L���­�pˇ�x6&��My��sҐ����\"�̀�R�IWU`c���}l<|�~�w\"��vI%r+��R�\n\\����][��6�&���ȭ�a�Ӻ��j�(ړ�Tѓ��C'��� '%de,�\n�FC�эe9C�N�Ѝ�-6�Ueȵ��CX��V������+�R+�����3B��ڌJ�虜��T2�]�\0P�a�t29��(i�#�aƮ1\"S�:�����oF)k�f���Ъ\0�ӿ��,��w�J@��V򄎵�q.e}KmZ����XnZ{G-���ZQ���}��׶�6ɸ���_�؁Չ�\n�@7�` �C\0]_ ��ʵ����}�G�WW: fCYk+��b۶���2S,	ڋ�9�\0﯁+�W�Z!�e��2������k.Oc��(v̮8�DeG`ۇ�L���,�d�\"C���B-�İ(����p���p�=����!�k������}(���B�kr�_R�ܼ0�8a%ۘL	\0���b������@�\"��r,�0T�rV>����Q��\"�r��P�&3b�P��-�x���uW~�\"�*舞�N�h�%7���K�Y��^A����C����p����\0�..`c��+ϊ�GJ���H���E����l@|I#Ac��D��|+<[c2�+*WS<�r��g���}��>i�݀�!`f8�(c����Q�=f�\n�2�c�h4�+q���8\na�R�B�|�R����m��\\q��gX����ώ0�X�`n�F���O p��H�C��jd�f��EuDV��bJɦ��:��\\�!mɱ?,TIa���aT.L�]�,J��?�?��FMct!a٧R�F�G�!�A���rr�-p�X��\r��C^�7���&�R�\0��f�*�A\n�՛H��y�Y=���l�<��A�_��	+��tA�\0B�<Ay�(fy�1�c�O;p���ᦝ`�4СM��*��f�� 5fvy {?���:y��^c��u�'���8\0��ӱ?��g��� 8B��&p9�O\"z���rs�0��B�!u�3�f{�\0�:�\n@\0����p���6�v.;�����b�ƫ:J>˂��-�B�hkR`-����aw�xEj����r�8�\0\\����\\�Uhm� �(m�H3̴�S����q\0��NVh�Hy�	��5�M͎e\\g�\n�IP:Sj�ۡٶ�<���x�&�L��;nfͶc�q��\$f�&l���i�����0%yΞ�t�/��gU̳�d�\0e:��h�Z	�^�@��1��m#�N��w@��O��zG�\$�m6�6}��ҋ�X'�I�i\\Q�Y���4k-.�:yz���H��]��x�G��3��M\0��@z7���6�-DO34�ދ\0Κ��ΰt\"�\"vC\"Jf�Rʞ��ku3�M��~����5V ��j/3���@gG�}D���B�Nq��=]\$�I��Ӟ�3�x=_j�X٨�fk(C]^j�M��F��ա��ϣCz��V��=]&�\r�A<	������6�Ԯ�״�`jk7:g��4ծ��YZq�ftu�|�h�Z��6��i〰0�?��骭{-7_:��ސtѯ�ck�`Y��&���I�lP`:�� j�{h�=�f	��[by��ʀoЋB�RS���B6��^@'�4��1U�Dq}��N�(X�6j}�c�{@8���,�	�PFC���B�\$mv���P�\"��L��CS�]����E���lU��f�wh{o�(��)�\0@*a1G� (��D4-c��P8��N|R���VM���n8G`e}�!}���p�����@_���nCt�9��\0]�u��s���~�r��#Cn�p;�%�>wu���n�w��ݞ�.���[��hT�{��值	�ˁ��J���ƗiJ�6�O�=������E��ٴ��Im���V'��@�&�{��������;�op;^��6Ŷ@2�l���N��M��r�_ܰ�Í�` �( y�6�7�����ǂ��7/�p�e>|��	�=�]�oc����&�xNm���烻��o�G�N	p����x��ý���y\\3����'�I`r�G�]ľ�7�\\7�49�]�^p�{<Z��q4�u�|��Qۙ��p���i\$�@ox�_<���9pBU\"\0005�� i�ׂ��C�p�\n�i@�[��4�jЁ�6b�P�\0�&F2~������U&�}����ɘ	��Da<��zx�k���=���r3��(l_���FeF���4�1�K	\\ӎld�	�1�H\r���p!�%bG�Xf��'\0���	'6��ps_��\$?0\0�~p(�H\n�1�W:9�͢��`��:h�B��g�B�k��p�Ɓ�t��EBI@<�%����` �y�d\\Y@D�P?�|+!��W��.:�Le�v,�>q�A���:���bY�@8�d>r/)�B�4���(���`|�:t�!����?<�@���/��S��P\0��>\\�� |�3�:V�uw���x�(����4��ZjD^���L�'���C[�'�����jº[�E�� u�{KZ[s���6��S1��z%1�c��B4�B\n3M`0�;����3�.�&?��!YA�I,)��l�W['��ITj���>F���S���BбP�ca�ǌu�N����H�	LS��0��Y`���\"il�\r�B���/����%P���N�G��0J�X\n?a�!�3@M�F&ó����,�\"���lb�:KJ\r�`k_�b��A��į��1�I,�����;B,�:���Y%�J���#v��'�{������	wx:\ni����}c��eN���`!w��\0�BRU#�S�!�<`��&v�<�&�qO�+Σ�sfL9�Q�Bʇ����b��_+�*�Su>%0�����8@l�?�L1po.�C&��ɠB��qh�����z\0�`1�_9�\"���!�\$���~~-�.�*3r?�ò�d�s\0����>z\n�\0�0�1�~���J����|Sޜ��k7g�\0��KԠd��a��Pg�%�w�D��zm�����)����j�����`k���Q�^��1���+��>/wb�GwOk���_�'��-CJ��7&����E�\0L\r>�!�q́���7����o��`9O`�����+!}�P~E�N�c��Q�)��#��#�����������J��z_u{��K%�\0=��O�X�߶C�>\n���|w�?�F�����a�ϩU����b	N�Y��h����/��)�G��2���K|�y/�\0��Z�{��P�YG�;�?Z}T!�0��=mN����f�\"%4�a�\"!�ޟ����\0���}��[��ܾ��bU}�ڕm��2�����/t���%#�.�ؖ��se�B�p&}[˟��7�<a�K���8��P\0��g��?��,�\0�߈r,�>���W����/��[�q��k~�CӋ4��G��:��X��G�r\0������L%VFLUc��䑢��H�ybP��'#��	\0п���`9�9�~���_��0q�5K-�E0�b�ϭ�����t`lm����b��Ƙ; ,=��'S�.b��S���Cc����ʍAR,����X�@�'��8Z0�&�Xnc<<ȣ�3\0(�+*�3��@&\r�+�@h, ��\$O���\0Œ��t+>����b��ʰ�\r�><]#�%�;N�s�Ŏ����*��c�0-@��L� >�Y�p#�-�f0��ʱa�,>��`����P�:9��o���ov�R)e\0ڢ\\����\nr{îX����:A*��.�D��7�����#,�N�\r�E���hQK2�ݩ��z�>P@���	T<��=�:���X�GJ<�GAf�&�A^p�`���{��0`�:���);U !�e\0����c�p\r�����:(��@�%2	S�\$Y��3�hC��:O�#��L��/����k,��K�oo7�BD0{���j��j&X2��{�}�R�x��v���أ�9A����0�;0�����-�5��/�<�� �N�8E����	+�Ѕ�Pd��;���*n��&�8/jX�\r��>	PϐW>K��O��V�/��U\n<��\0�\nI�k@��㦃[��Ϧ²�#�?���%���.\0001\0��k�`1T� ����ɐl�������p���������< .�>��5��\0��	O�>k@Bn��<\"i%�>��z��������3�P�!�\r�\"��\r �>�ad���U?�ǔ3P��j3�䰑>;���>�t6�2�[��޾M\r�>��\0��P���B�Oe*R�n���y;� 8\0���o�0���i���3ʀ2@����?x�[����L�a����w\ns����A��x\r[�a�6�clc=�ʼX0�z/>+����W[�o2���)e�2�HQP�DY�zG4#YD����p)	�H�p���&�4*@�/:�	�T�	���aH5���h.�A>��`;.���Y��a	���t/ =3��BnhD?(\n�!�B�s�\0��D�&D�J��)\0�j�Q�y��hDh(�K�/!�>�h,=�����tJ�+�S��,\"M�Ŀ�N�1�[;�Т��+��#<��I�Zğ�P�)��LJ�D��P1\$����Q�>dO��v�#�/mh8881N:��Z0Z���T �B�C�q3%��@�\0��\"�XD	�3\0�!\\�8#�h�v�ib��T�!d�����V\\2��S��Œ\nA+ͽp�x�iD(�(�<*��+��E��T���B�S�CȿT���� e�A�\"�|�u�v8�T\0002�@8D^oo�����|�N������J8[��3����J�z׳WL\0�\0��Ȇ8�:y,�6&@�� �E�ʯݑh;�!f��.B�;:���[Z3������n���ȑ��A���qP4,��Xc8^��`׃��l.����S�hޔ���O+�%P#Ρ\n?��IB��eˑ�O\\]��6�#��۽؁(!c)�N����?E��B##D �Ddo��P�A�\0�:�n�Ɵ�`  ��Q��>!\r6�\0��V%cb�HF�)�m&\0B�2I�5��#]���D>��3<\n:ML��9C���0��\0���(ᏩH\n����M�\"GR\n@���`[���\ni*\0��)������u�)��Hp\0�N�	�\"��N:9q�.\r!���J��{,�'����4�B���lq���Xc��4��N1ɨ5�Wm��3\n��F��`�'��Ҋx��&>z>N�\$4?����(\n쀨>�	�ϵP�!Cq͌��p�qGLqq�G�y�H.�^��\0z�\$�AT9Fs�Ѕ�D{�a��cc_�G�z�)� �}Q��h��HBָ�<�y!L����!\\�����'�H(��-�\"�in]Ğ���\\�!�`M�H,gȎ�*�Kf�*\0�>6���6��2�hJ�7�{nq�8����H�#c�H�#�\r�:��7�8�܀Z��ZrD��߲`rG\0�l\n�I��i\0<����\0Lg�~���E��\$��P�\$�@�PƼT03�HGH�l�Q%*\"N?�%��	��\n�CrW�C\$��p�%�uR`��%��R\$�<�`�Ifx���\$/\$�����\$���O�(���\0��\0�RY�*�/	�\rܜC9��&hh�=I�'\$�RRI�'\\�a=E����u·'̙wI�'T���������K9%�d����!��������j�����&���v̟�\\=<,�E��`�Y��\\����*b0>�r��,d�pd���0DD ̖`�,T �1�% P���/�\r�b�(���J����T0�``ƾ����J�t���ʟ((d�ʪ�h+ <Ɉ+H%i�����#�`� ���'��B>t��J�Z\\�`<J�+hR���8�hR�,J]g�I��0\n%J�*�Y���JwD��&ʖD�������R�K\"�1Q�� ��AJKC,�mV�������-���KI*�r��\0�L�\"�Kb(����J:qKr�d�ʟ-)��ˆ#Ը�޸[�A�@�.[�Ҩʼ�4���.�1�J�.̮�u#J���g\0��򑧣<�&���K�+�	M?�/d��%'/��2Y��>�\$��l�\0��+����}-t��ͅ*�R�\$ߔ��K�.����JH�ʉ�2\r��B���(P���6\"��nf�\0#Ї ��%\$��[�\n�no�LJ�����e'<����1K��y�Y1��s�0�&zLf#�Ƴ/%y-�ˣ3-��K��L�΁��0����[,��̵,������0���(�.D��@��2�L+.|�����2�(�L�*��S:\0�3����G3l��aːl�@L�3z4�ǽ%̒�L�3����!0�33=L�4|ȗ��+\"���4���7�,\$�SPM�\\��?J�Y�̡��+(�a=K��4���C̤<Ё�=\$�,��UJ]5h�W�&t�I%��5�ҳ\\M38g�́5H�N?W1H��^��Ը�Y͗ؠ�͏.�N3M�4Å�`��i/P�7�dM>�d�/�LR���=K�60>�I\0[��\0��\r2���Z@�1��2��7�9�FG+�Ҝ�\r)�hQtL}8\$�BeC#��r*H�۫�-�H�/���6��\$�RC9�ب!���7�k/P�0Xr5��3D���<T�Ԓq�K���n�H�<�F�:1SL�r�%(��u)�Xr�1��nJ�I��S�\$\$�.·9��IΟ�3 �L�l���Ι9��C�N�#ԡ�\$�/��s��9�@6�t���N�9���N�:����7�Ӭ�:D���M)<#���M}+�2�N��O&��JNy*���ٸ[;���O\"m����M�<c�´���8�K�,���N�=07s�JE=T��O<����J�=D��:�C<���ˉ=���K�ʻ̳�L3�����LTЀ3�S,�.���q-��s�7�>�?�7O;ܠ`�OA9���ϻ\$���O�;��`9�n�I�A�xp��E=O�<��5����2�O�?d�����`N�iO�>��3�P	?���O�m��S�M�ˬ��=�(�d�Aȭ9���\0�#��@��9D����&���?����i9�\n�/��A���ȭA��S�Po?kuN5�~4���6���=򖌓*@(�N\0\\۔dG��p#��>�0��\$2�4z )�`�W���+\0��80�菦������z\"T��0�:\0�\ne \$��rM�=�r\n�N�P�Cmt80�� #��J=�&��3\0*��B�6�\"������#��>�	�(Q\n���8�1C\rt2�EC�\n`(�x?j8N�\0��[��QN>���'\0�x	c���\n�3��Ch�`&\0���8�\0�\n���O`/����A`#��Xc���D �tR\n>���d�B�D�L��������Dt4���j�p�GAoQoG8,-s����K#�);�E5�TQ�G�4Ao\0�>�tM�D8yRG@'P�C�	�<P�C�\"�K\0��x��~\0�ei9���v))ѵGb6���H\r48�@�M�:��F�tQ�!H��{R} �URp���O\0�I�t8������[D4F�D�#��+D�'�M����>RgI���Q�J���U�)Em���TZ�E�'��iE����qFzA��>�)T�Q3H�#TL�qIjNT���&C��h�X\nT���K\0000�5���JH�\0�FE@'љFp�hS5F�\"�oѮ�e%aoS E)� ��DU��Q�Fm�ѣM��Ѳe(tn� �U1ܣ~>�\$��ǂ��(h�ǑG�y`�\0��	��G��3�5Sp(��P�G�\$��#��	���N�\n�V\$��]ԜP�=\"RӨ?Lzt��1L\$\0��G~��,�KN�=���GM����NS�)��O]:ԊS}�81�RGe@C�\0�OP�S�N�1��T!P�@��S����S�G`\n�:��P�j�7R� @3��\n� �������DӠ��L�����	��\0�Q5���CP��SMP�v4��?h	h�T�D0��֏��>&�ITx�O�?�@U��R8@%Ԗ��K���N�K��RyE�E#�� @����%L�Q�Q����?N5\0�R\0�ԁT�F�ԔR�S�!oTE�C(�����ĵ\0�?3i�SS@U�QeM��	K�\n4P�CeS��\0�NC�P��O�!�\"RT�����S�N���U5OU>UiI�PU#UnKP��UYT�*�C��U�/\0+���)��:ReA�\$\0���x��WD�3���`����U5�IHUY��:�P	�e\0�MJi�����Q�>�@�T�C{��u��?�^�v\0WR�]U}C��1-5+U�?�\r�W<�?5�JU-SX��L�� \\t�?�sM�b�ՃV܁t�T�>�MU+�	E�c���9Nm\rRǃC�8�S�X�'R��XjCI#G|�!Q�Gh�t�Q��� )<�Y�*��RmX0����M���OQ�Y�h���du���Z(�Ao#�NlyN�V�Z9I���M��V�ZuOՅT�T�EՇַS�e����\n�X��S�QER����[MF�V�O=/����>�gչT�V�oU�T�Z�N�*T\\*����S-p�S��V�q��M(�Q=\\�-UUUV�C���Z�\nu�V\$?M@U�WJ\r\rU��\\�'U�W]�W��W8�N�'#h=oC���F(��:9�Yu����V-U�9�]�C�:U�\\�\n�qW���(TT?5P�\$ R3�⺟C}`>\0�E]�#R��	��#R�)�W���:`#�G�)4�R��;��ViD%8�)Ǔ^�Q��#�h	�HX	��\$N�x��#i x�ԒXR��'�9`m\\���\nE��Q�`�bu@��N�dT�#YY����GV�]j5#?L�xt/#���#酽O�P��Q��6����^� �������M\\R5t�Ӛp�*��X�V\"W�D�	oRALm\rdG�N	����6�p\$�P废E5����Tx\n�+��C[��V�����8U�Du}ػF\$.��Q-;4Ȁ�NX\n�.X�b͐�\0�b�)�#�N�G4K��ZS�^״M�8��d�\"C��>��dHe\n�Y8���.� ���ҏF�D��W1cZ6��Q�KH�@*\0�^���\\Q�F�4U3Y|�=�Ӥ�E��ۤ�?-�47Y�Pm�hYw_\r�VeױM���ُe(0��F�\r�!�PUI�u�7Q�C�ю?0����gu\rqधY-Q�����=g\0�\0M#�U�S5Zt�֟ae^�\$>�ArV�_\r;t���HW�Z�@H��hzD��\0�S2J� HI�O�'ǁe�g�6�[�R�<�?� /��KM����\n>��H�Z!i����TX6���i�C !ӛg�� �G }Q6��4>�w�!ڙC}�VB�>�UQڑj�8c�U�T���'<�>����HC]�V��7jj3v���`0���23����x�@U�k�\n�:Si5��#Y�-w����M?c��MQ�GQ�уb`��\0�@��ҧ\0M��)ZrKX�֟�Wl������l�TM�D\r4�QsS�40�sQ́�mY�h�d��C`{�V�gE�\n��XkՁ�'��,4���^�6�#<4��NXnM):��OM_6d�������[\"KU�n��?l�x\0&\0�R56�T~>��ո?�Jn��� ��Z/i�6���glͦ�U��F}�.����JL�CTbM�4��cL�TjSD�}Jt���Z����:�L���d:�Ez�ʤ�>��V\$2>����[�p�6��R�9u�W.?�1��RHu���R�?58Ԯ��D��u���p�c�Z�?�r׻ Eaf��}5wY���ϒ���W�wT[Sp7'�_aEk�\"[/i��#�\$;m�fأWO����F�\r%\$�ju-t#<�!�\n:�KEA����]�\nU�Q�KE��#��X��5[�>�`/��D��֭VEp�)��I%�q���n�x):��le���[e�\\�eV[j�����7 -+��G�WEwt�WkE�~u�Q/m�#ԐW�`�yu�ǣD�A�'ױ\r��ՙO�D )ZM^��u-|v8]�g��h���L��W\0���6�X��=Y�d�Q�7ϓ��9����r <�֏�D��B`c�9���`�D�=wx�I%�,ᄬ�����j[њ����O��� ``��|�����������.�	AO���	��@�@ 0h2�\\�ЀM{e�9^>���@7\0��˂W���\$,��Ś�@؀����w^fm�,\0�yD,ם^X�.�ֆ�7����2��f;��6�\n����^�zC�קmz��n�^���&LFF�,��[��e��aXy9h�!:z�9c�Q9b� !���Gw_W�g�9���S+t���p�tɃ\nm+����_�	��\\���k5���]�4�_h�9 ��N����]%|��7�֜�];��|���X��9�|����G���[��\0�}U���MC�I:�qO�Vԃa\0\r�R�6π�\0�@H��P+r�S�W���p7�I~�p/��H�^������E�-%��̻�&.��+�Jђ;:���!���N�	�~����/�W��!�B�L+�\$��q�=��+�`/Ƅe�\\���x�pE�lpS�JS�ݢ��6��_�(ů���b\\O��&�\\�59�\0�9n���D�{�\$���K��v2	d]�v�C�����?�tf|W�:���p&��Ln��賞�{;���G�R9��T.y���I8���\rl� �	T��n�3���T.�9��3����Z�s����G����:	0���z��.�]��ģQ�?�gT�%��x�Ռ.����n<�-�8B˳,B��rgQ�����Ɏ`��2�:{�g��s��g�Z��� ׌<��w{���bU9�	`5`4�\0BxMp�8qnah�@ؼ�-�(�>S|0�����3�8h\0���C�zLQ�@�\n?��`A��>2��,���N�&��x�l8sah1�|�B�ɇD�xB�#V��V�׊`W�a'@���	X_?\n�  �_�. �P�r2�bUar�I�~��S���\0ׅ\"�2����>b;�vPh{[�7a`�\0�˲j�o�~���v��|fv�4[�\$��{�P\rv�BKGbp������O�5ݠ2\0j�لL���)�m��V�ejBB.'R{C��V'`؂ ��%�ǀ�\$�O��\0�`����4 �N�>;4���/�π��*��\\5���!��`X*�%��N�3S�AM���Ɣ,�1����\\��caϧ ��@��˃�B/����0`�v2��`hD�JO\$�@p!9�!�\n1�7pB,>8F4��f�π:��7���3��3����T8�=+~�n���\\�e�<br����Fز� ��C�N�:c�:�l�<\r��\\3�>���6�ONn��!;��@�tw�^F�L�;���,^a��\ra\"��ڮ'�:�v�Je4�א;��_d\r4\r�:����S�����2��[c��X�ʦPl�\$�ޣ�i�w�d#�B��b��������`:���~ <\0�2����R���P�\r�J8D�t@�E��\0\r͜6����7����Y���\"����\r�����3��.�+�z3�;_ʟvL����wJ�94�I�Ja,A����;�s?�N\nR��!��ݐ�Om�s�_��-zۭw���zܭ7���z���M����o����\0��a��ݹ4�8�Pf�Y�?��i��eB�S�1\0�jDTeK��UYS�?66R	�c�6Ry[c���5�]B͔�R�_eA)&�[凕XYRW�6VYaeU�fYe�w��U�b�w�E�ʆ;z�^W�9��ק�ݖ��\0<ޘ�e�9S���da�	�_-��L�8ǅ�Q��TH[!<p\0��Py5�|�#��P�	�9v��2�|Ǹ��fao��,j8�\$A@k����a���b�c��f4!4���cr,;�����b�=��;\0��ź���cd��X�b�x�a�Rx0A�h�+w�xN[��B��p���w�T�8T%��M�l2�������}��s.kY��0\$/�fU�=��s�gK���M� �?���`4c.��!�&�分g��f�/�f1�=��V AE<#̹�f\n�)���Np��`.\"\"�A�����q��X��٬:a�8��f��Vs�G��r�:�V��c�g�Vl��g=��`��W���y�gU��˙�Ẽ�eT=�����x 0� M�@����%κb���w��f��O�筘�*0���|t�%��P��p��gK���?p�@J�<Bٟ#�`1��9�2�g�!3~����nl��f��Vh���.����aC���?���-�1�68>A��a�\r��y�0��i�J�}�������z:\r�)�S���@��h@���Y���mCEg�cyφ��<���h@�@�zh<W��`��:zO���\r��W���V08�f7�(Gy���`St#��f�#����C(9���؀d���8T:���0�� q���79��phAg�6�.��7Fr�b� �j��A5��a1��h�ZCh:�%��gU��D9��Ɉ�׹��0~vTi;�VvS��w��\r΃?��f�����n�ϛiY��a��3�·9�,\n��r��,/,@.:�Y>&��F�)�����}�b���iO�i��:d�A�n��c=�L9O�h{�� 8hY.������������\r��և�����1Q�U	�C�h��e�O���+2o����N�����zp�(�]�h��Z|�O�c�zD���;�T\0j�\0�8#�>Ύ�=bZ8Fj���;�޺T酡w��)���N`���ÅB{��z\r�c���|dTG�i�/��!i��0���'`Z:�CH�(8�`V������\0�ꧩ��W��Ǫ��zgG������-[��	i��N\rq��n���o	ƥfEJ��apb��}6���=o���,t�Y+��EC\r�Px4=����@���.��F��[�zq���X6:FG��#��\$@&�ab��hE:����`�S�1�1g1���2uhY��_:Bߡdc�*���\0�ƗFYF�:���n���=ۨH*Z�Mhk�/�냡�zٹ]��h@����1\0��ZK�������^+�,vf�s��>���O�|���s�\0֜5�X��ѯF��n�A�r]|�Ii4�� ��C� h@ع����cߥ�6smO������gX�V2�6g?~��Y�Ѱ�s�cl \\R�\0��c��A+�1������\n(����^368cz:=z��(�� ;裨�s�F�@`;�,>yT��&��d�Lן��%��-�CHL8\r��b�����Mj]4�Ym9����Z�B��P}<���X���̥�+g�^�M� + B_Fd�X���l�w�~�\r⽋�\":��qA1X������3�ΓE�h�4�ZZ��&����1~!N�f��o���\nMe�଄��XI΄�G@V*X��;�Y5{V�\n���T�z\rF�3}m��p1�[�>�t�e�w����@V�z#��2��	i���{�9��p̝�gh���+[elU���A�ٶӼi1�!��omm�*K���}��!�Ƴ��{me�f`��m��C�z=�n�:}g� T�mLu1F��}=8�Z���O��mFFMf��OO����������/����ޓ���V�oqj���n!+����Z��I�.�9!nG�\\��3a�~�O+��::�K@�\n�@���Hph��\\B��dm�fvC���P�\" ��.nW&��n��HY�+\r���z�i>Mfqۤ��Qc�[�H+��o��*�1'��#āEw�D_X�)>�s��-~\rT=�������- �y�m����{�h��j�M�)�^����'@V�+i�������;F��D[�b!����B	��:MP���ۭoC�vAE?�C�IiY��#�p�P\$k�J�q�.�07���x�l�sC|���bo�2�X�>M�\rl&��:2�~��cQ����o��d�-��U�Ro�Y�nM;�n�#��\0�P�f��Po׿(C�v<���[�o۸����fѿ���;�ẖ�[�Y�.o�Up���pU���.���B!'\0���<T�:1�������<���n��F���I�ǔ��V0�ǁRO8�w��,aF��ɥ�[�Ο��YO����/\0��ox���Q�?��:ً���`h@:�����/M�m�x:۰c1������v�;���^���@��@�����\n{�����;���B��8�� g坒�\\*g�yC)��E�^�O�h	���A�u>���@�D��Y�����`o�<>��p���ķ�q,Y1Q��߸��/qg�\0+\0���D���?�� ����k:�\$����ץ6~I��=@���!��v�zO񁚲�+���9�i����a������g������?��0Gn�q�]{Ҹ,F���O���� <_>f+��,��	���&�����·�y�ǩO�:�U¯�L�\n�úI:2��-;_Ģ�|%�崿!��f�\$���Xr\"Kni����\$8#�g�t-��r@L�圏�@S�<�rN\n�D/rLdQk࣓�����e����Э��\n=4)�B���ך��Z-|Hb����Hk�*	�Q!�'��G ��Ybt!��(n,�P�Ofq�+X�Y����\"b F6��r f�\"�ܳ!N��^��r�B_(�\"�K�_-<��*Q���/,)�H\0����r�\"z2(�tه.F>��#3���268sh٠��ƑI1Sn20���-��4���2A�s(�4�˶��\0��#��r�K'�ͷG'�7&\n>x���J�GO8,�0���8���\0�W9��I�?:3n�\r-w:�����;3ȉ�!�;��ꃘ�Z�RM�+>�����0/=R�'1�4�8����m�%ȥ}χ9�;�=�nQ��=�hhL��G�kW�\r�	%�4Ҝs�ΖJ�3s�4�@�U�%\$���N;�?4���N��2|��Z�3�h\0�3�5�^�xi2d\r|�M�ʣbh|�#v�` \0�ꐮ���\$\r2h#���?���I\n���+o-��?6`ṽ�.\$���KY%�J?�c�R�N#K:�K�EL�>:��@��jP��n_t&slm�'�ЩɸӜ�����;6ۗHU5#�Q7U��WY�U bN��W�_���;TC�[�<ږ>����W�CU��6X#`MI:t�ӵ��	u#`�fu�\$�t���X�`�f<�;b�gh���9�7�S58���#^�-�\0����չR*�'��(���qZ壣�X�Q�FUv�W GW���T��W�~ڭ^�W�����J=_ؗbm��bV\\l��/�M��TmTOXu�=_��ITvvu�a\rL_�qR/]]m�su=H=u�g o\\UՅgM�	XVU��%�h��53U�\\=��Q��M�v���g�m��ue�����h�b�M�GCeO5�ԁ�O5��Y�i=e�	G�TURvOa�*�ivWX�J5<��bu�]������<����\$u3v#�'e�u�R5m��v�D5�.v���W=�U_�(�\\V��_<��S�n)�1M%Qh�Z�T�f5E�'��W��v�UmiՂU��]aW�U�dRv��-YUZu��UV��UiR�V������[��ZMU�\\=�v{�X���wQ�huHv��gqݴw!�oqt�U{TGq�{�#^G_ubQ���i9Qb>�NUd��k��5hP�mu[�\0����_��[�Y-����r���(�CrMe�J�!h?QrX3 x���#��x�<�{u5~���-�u��YyQ\r-��\0�uգuuٿpUڅ�)�P��\r<u�S�0��w��-i���!�֊�B���d]��Ň��E��vlmQݏ6k��J��w�Ğ����ED�U�R�e�v:X�c�NW}`-�t�H#e��b��u���	~B7� ?�	OP�CW���SE͕V>���U�7�����m�ӂ�z�=����1���+��m�I,>�X7��]�.��*	^��N��.��/\"���)�	���s��|��ӟ�l�}�����!�5n�p�j��h�}���m�E�zH�aO0d=A|w�߳������u���v���G�x#��b�cS�o-��tOm`C��^M��@�h�n\$k�`�`HD^�PE�[�]��rR�m�=�.�ه>Ayi� \"���	��o�-,.�\nq+���fXd����*߽�K�؃'�� �%a����9p���KLM��!�,������zX#�V�uH%!��63�J�ryՁ��q_�u	�W����|@3b1��7|~wﱳ��A7���	��9cS&{���%Vx��kZO��w�Ur?����N �|�C�#Ű��կ �/��9�ft�Ew�C��a�^\0�O<�W�{Y�=�e��n���gyf0h@�S�\0:C���^��VgpE9:85�3�ާ���@��j_�[�+��ǩx�^�ꮆ~@чW���㓜�9x�FC���.�����k^I���pU9��S������\$���\r4���\0��O���)L[�p?�.PECS�I1nm{�?�P�WA߲�;���D�;S�a�Kf��%�?�X��+��B>��9���Gj�c�z�A͎�:�a�n0bJ{o��!3��!'��K�����}�\\��3W��5�x���L;�2ζn�a;���׺Xӛ]�o��x�{�5ޙjX���vӚ��q��EE{р4����{���	�\n��>��aﯷ�����L����������'����{�\n��>J�ߌ��ӗ��Y�\rOʽ�t����-O���4��9F�;�����G��I�F��1�o����O���a{w�0����Ư;񔄑l�o��J�Tb\rw�2�J��=D#�n�:�y��S�^�,.�?(�I\$���Ư��3��s�4M�aCR���G̑��I߰n<�zy�XN��?��.��=���DǼ�\r����\n��\ro��\nПCl%��Y���߰��G���}#�VН%�(����3�ɍ�r��};��׿G��n�[�{����_<m4[	I����q��?�0cV�nms��nM���\"Nj1�w?@�\$1��>��^�����\\�{n�\\���7���ٟic1���hoo�?j<G�x�l���S�r}���|\"}��/�?s��tI���&^�1e��t��,�*'F��=�/F�k�,95rV������쑈��o9��/F��_�~*^��{�I����_�����^n���N��~���A�d����U�w�qY���T�2��G�?�&����:y��%��X�J�C�d	W�ߎ~�G!��J}��������B-��;���h�*�R���E��~���.�~���SAqDVx���='��E�(^���~����������o7~�M[��Q��(��y��nP�>[WX{q�aϤ���.&N�3]��HY������[���&�8?�3������݆����#���B�e�6��@��[������G\r�+��}������_��7�|N����4~(z�~����%��?����[��1�S�]x�k��KxO^�A���rZ+����*�W��k�wD(���R:��\0����'����m!O�\n��u���.�[ �P�!��}��m ��1p�u��,T��L 	0}��&P٥\n�=D�=���\rA/�o@��2�t�6�DK��\0���q�7�l���B���(�;[��kr\r�;#���lŔ\r�<}zb+��O�[�WrX�`�Z ţ�Pm'Fn����Sp�-�\0005�`d���P���Ǿ��;��n\0�5f�P���EJ�w�� �.?�;��N�ޥ,;Ʀ�-[7��e��i��-���dَ<[~�6k:&�.7�]�\0������/�59 ��@eT:煘�3�d�sݝ�5䏜5f\0�P��HB�����8J�LS\0vI\0���7Dm��a�3e��?B��\$�.E���f���@�n���b�Gb��q3�|��Paˈ�ϯX7Tg>�.�p�5��AHŵ��3S�,��@�#&w��3��m[���I�ѥ�^�̤J1?�gTၽ#�S�=_��_��	���Vq/C۾�݀�|�����D �g>܄��� 6\r�7}q��Ť�JG�B^�\\g������&%��[�2Ixì��6\03]�3�{�@RU��M��v<�1����sz�uP�5��F:�i�|�`�q���V| ��\nk��}�'|�gd�!�8� <,�P7�m��||���I�A��]BB �F�0X���	�D��`W���qm�OL�	�.�(�p��ҁ��\"!����\0��A����V��7k��M�\$�N0\\���\"�f������\0uq��,��5��A6�p���\n�ΐjY�7[pK��4;�l�5n��@�\\f��l	��M���P��3��C�HbЌ��cEpP���4eooe�{\r-��2.�֥��P50u���G}��\0����<\r��!��~�������\n7F��d�����>��a��%�c6Ԟ��M��|��d����O�_�?J��C0�>Ё�&7kM4�`%f�l�ΘB~�wx��ZG�P�2��0�=�*p��@�BeȔ��|2�\r�?q��8����Њ(�yr���0��>�>�E?w�|r]�%Av�����@�+�X��Ag����s��C��AXmNҝ�4\0\r���8J�J�ǸD�Қ�:=	������S�4��F;	�\\&��P!6%\$i�xi4c�0B�;62=��1��̈PC��m���dpc+�5��\$/rCR�`�MQ�6(\\��2A���\\��lG�l�\0Bq��P�r���B����т�_6Ll�!BQ��IG�����XRbs�]B�Hr���`�X��\$p�8���	nbR,±�L��\"�E%\0�aYB�s���D,�!��ϛpN9RbG�4��M��t����jU�����y\0��%\$.�iL!x��ғ�(�.�)6T(�I��a%�K�]m�t���&��G7�ITM�B�\rza��])va�%���41T�j͹(!�����\\�\\�W��\\t\$�0��%�\0aK\$�T�F(Y�C@��H���H�nD�d��Wp��hZ�'�ZC,/���\$����J�FB�uܬQ:Υ�A��:-a#��=jb��l�Ug;{R��U��EWn�Ua��V��Nj��u�G�*�yֹ%��@��*���Yx�_�z�]�)v\"��R��L�VIv�=`��'��U�) S\r~R���\ni��)5S��D49~�b�;)3�,�9M3�HsJkT�Ü�(����uJ�][\$uf��ob���\n.,�Yܵ9j1'��!�1�\$J��gڤ՟ĆU0��Zuah���cH��,�Yt��Kb�5��5��/dY��AU�҅��[W>�_V�\r��*���j��-T�� z�Y�d�c�m�ҹ��:����[Ut-{���l	�i+a)�.[��_:�5��h��W§�m��%JI��[T�h>�������;�X̺d�S�d�V�;\rƱ!N��K&�A�Ju4B��dg΢.Vp��mb��)�V!U\0G丨��`���\\��q�7Q�b�VL��:�Ղ���Z.�N��*�ԏU]Z�l�z������R D1I��£�r:\0<1~;#�Jb���M�y�+�۔/�\"ϛj<3�#��̌��:P.}�e����D\"q�yJ�G���sop�����X�\r��d��\rxJ%���ƼO:%yy��,��%{�3<�Xø����z�E�z(\0 �D_���.2+�g�b�c�x�pgި��|9CP����48U	Q�/Aq��Q�(4 7e\$D��v:�V�b��N4[��iv���2�\r�X1��AJ(<PlF�\0���\\z�)���W�(�4����� p�����`��\r�da6����O��m�a�}q�`��6P�'h��3�|����f� j��A�z���+�D�UW�D���5��%#�x�3{��L\r-͙]:jd�P	j�f�q:Z�\"sad�)�G�3	��+��r�NK��1Q���x=>�\"��-�:�F���Iك*�@ԟ�y�T�\\U��Y~������3D������f,s�8HV�'�t9v(:��B9�\\Z����(�&�E8���W\$X\0�\n��9�WB��b��66j9� �ʈ��?,��| �a��g1�\nPs�\0@�%#K����\r\0ŧ\0���0�?�š,�\0��h��h�\08\0l\0�-�Z��jb�Ŭ\0p\0�-�f`ql��0\0i-�\\ps��7�e\"-Z�lb�E�,�\0��]P ��E��b\0�/,Z��\r�\0000�[f-@\rӯEڋ�/�Z8��~\"��ڋ��.^��Qw��ϋ�\0�/t_ȼ���E���\0�0d]��b�Ť�|\0��\\ؼ���E�\0af0tZ��n�J�\0l\0�0L^��Qj@��J��^��q#F(�1�/�[�1�����I�.�^8��\0[�q��[Ñl\"�� ��\0�0,d����\r����c��{cE�\0o�0�]�\0\rc%�ۋ���8�w���Z��-�\\��{��֋G�/\\bp��@1�\0a�1�����s�!Ũ�/�/�]8��~c\"�ۋ��2�cΑm�\"�9�q�/\\^fQ~c�_���-\$i�\"�\0003����fX�qx#\09��Z.�i���@F���3tZH� \rcK�b\0j�/Dj��1����I�h�a��v�Ʃ�OZ4�Z��т#YE�\0i�.hH��sX/F<���.�j���b���\0mV/d\\���b�E����3T^(�шcKFR�����]X�q��������6�]h��c6Eċ�66�h����n\0005�sn/dn��`\r\"�F���-D`�Ց��N�2�Y��bx��#\\�닇V3x�1x�Fx��\0�6�b�q����!��8|^���ub�����-�r��q��:��%�0�pp�#����\0�6�f��Ǣ�Ŭ�d�0�qH����\$�@�q�-�^B4��\"�\08�1�/lnxϑ���G�3:0tjh�~@Ƽ���3�vH��b�G(�e��4gغq��2�1��-�nX��\"�F<�Q�1\\j��1���Eǋ��4m����[�n�z7�yh�1�#�ގ/�3\\x�q�KG����6�o��1{��FJ���6�lX�q⣄�u���9�r(�1��Gc\0�f:�rX��#�Ž\0i�<\\}���b�F�\0s�7�y2���#uFe��\">4i��������\n<{�㑍��Ɖ�J;�]��1�#��0��J;4^��D���Ǯ����4i��(H#��E�x�/�n��1��/ǡ��j6,l��1t�/\0005%�0�]x����GG5�!�0��������r�q�2��ޑ��NFP�o\"4�_��1�d�%�e �3�s8���G5�� �6�[H��c�H�jY�;�[辑�b�! �y�@�\\��q�#WHN���;�c�Q��:�-�%�.�kXƑ���G͌��1Df�ߑ�cWFl��!�0����c Eܐ��;l��q�\"�F����7\\\\������O�q�.T|\"?����E��f9TyYѩ�SG1���A\$f9R\n\"��x��>B��H��ߤ\0���:\$e�1���F?�=�3Tu)\nq�b��~���<T��α�c�H.�m~C�wHʱ�#/�I�]~3�^��ф#��>�Y�4�^��Qjc��K�1\"�8�|6��c\"�B��\"b4���%����G\0e\"�/t���1r�1��e!v2�y����<Ǡ���8\\o��ђ#t�ѐ\rz@�}H�b���y �1�\\���deG��Z3�~�r)�1ȿ���Bl~H��:�dF��-�?�k8�q�c(F͋�K�5|my�c1�<�*@�j���1��ž��>I�Z��Qj��2��\$0��h�Q��VFT�	\$�Al~�qڣȱ�\$�>\\p�\rq�\$/�u%�!�Jq \$��tE��GN-Tq)�\"��Hʌ��=�X�2-�H���8\\n��RW\$H��\"�C\\_�\0�d\$�f��\".D�u	'Q�zE��&0to��qj��ƿ��R@d������u�##�LLk�*q�\$*Gđi�@T�i�l��E����5���r\\d�I���\"/�Z�0�j\$T���z5Ld3�����o�.Tq�!1{�����9�Z��Q�b�F�wJ94n�����{�(�-�8�2h�u��;\$�-Dk��rs��H���#���Y7�\"�/E����	\$j�^�-�]�7�[\"N\$����W����/]�\$�+�1Ga�/&IDn�@\$��!��\$�-�k!�Q����)(N/\$t������O�KzP�tX��[\0�G��w(*K\$v��1�c�'��G̞I�xd��\n�A�8\\rX��a��I�iN�I%\$���_���6�f�Q�#��I�5#�F��غ��#�E⒕\"�3\$�I�c�H���vR|�Q��cE���:R�e��h�EΏfK`8�r.#�E��s�0L���R��F���!\nC\$`���\$�H?��nP�e�!�@F'���/�����������%�N,h��rF\$�����3�t��Ҁ���!1<��CQ�%�Ò��J�Z�f.�6ō����C���Ԝ.�[��Bҿx����\0NRn`���Y\n�%+N�IMs:ùYd�ef�B[���nƹY��m��R�ג��Y��C�X���j��U+Vk,�\0P��b@e���x��V��yT�7�u�[J�ȱ\nD��eR��mx&�l�\0)�}�J�,\0�I�ZƵ\$k!���Yb�����Re/Q���k�5.�e��5����W�`��\0)�Yv\"V�\0��\n�%��`Yn�աa��xÆQ!,�`\"�	_.�偩Ɩtm\$�\"��J��֍���v�%�M9j��	斧�*�Kp֔�;\\R ��3(���^��:}���|>µa-'U%w*�#>�@�̬e�J���;Pw/+��5E\rjn���d���^[���cΰ�u�z\\ؐ1mi\"x��p��;����P)����#��ؒ���!A�;��	4�a{`aV{K�U��8㨟0''o�2���yc̸9]K�@�җ^�lB��Or���,du��8�?����%�gB����Yn+�%c�e\0���ऱYr@f�(]ּ�\nbiz��n�SS2��GdBPj���@�(�ȥ�!�-�v��e�*c\0��4J�炒���,�U�	d��e�j'T�H]Ԋ�G!�)u��֯��ү�Z�B5�̓W��0\n���R���W��\\�Q j�^r�%l��3,�Yy��f3&��܎�Q:ϵ2�m�R)�T��(KR��0�ʔ@��Y��Y:��e3\r%���T�%�X����ST�.J\\�0�h�ą�D!�:�u���U\"�Ł�o+7�\"����f'��R\0���J��2S�2�#nm ��I劜�\"X���[�ր��} J��c�9p0���Q�(U\0�xDEW��.L��=<B�0+�)ZS V;�\\�I{�5I�A���,dW�u�5Ew\n\$%ҁ���2i_\$��+��O,����X��ՑJg&J��G��%\\J��b.��^L�T�Fl�薹]k#f@L�G�ĐT�ٗ��H��\"�q1S̰��j�V�(Ι��ZVz�ņ�,����G�.1F��gN�;�1ÊV��5E��5`�\0Ct�=F\nṛα�K����\0�ۊ�%��D]Q\$\r\0�3J\\,͙��<T4*���.�YK�D�Q��L�S%,�g������<��u0���Uĉ�*x(��NYv!��y�	w�4fd��rG��M \$��^;�����)<P�]D�%%�;�j��I0�a�u^Jp�[)�v�3RhR�E��\n�L_�#5|ܾ�m3P�*�\\Y51X��	i�N���\$\"��a���h*KU���V8��u�%&�r�˚��5o���g�;�rMl[ƨ�g������U�q�깚h|�eO2�f MlW2AP�׹�����v~eD�e�3Uӫl�E62i�����Ub���U���������V��iI!\$i�ʭ&Z:��xm!ņ�.�O�fwү!���kݤ̓��6b\"�I�J]]:T��6�Vr��}��ǫ]����U��	ys7f�Mř�3����Y��:T_M�w%3�n��\n��z*��3�h��	�`U��L���,�ۄ�5��vf��Û�42_Q��h���uD�\no��)�ĜիM9�7foۼ��r����WB~iT�eyQT�N\n�d�pr�#��M�;���4�p���t���(;���5	|��ǂ��',AV7ܔ��UA�&��R�P�\"��y�ҷ��)�[�n���-3V��,?�s6�p���3�f��A��9k|�ɮS�f�*@��5�g��ɿ2��}����U�ݙ����H�F�l%�p«Ie�be�M�SO\r�[��i�3�f��LV��r�u�����NA�:�%r��y3Q�_̸�W.���^Sl@&���5�Yl��1���}Vx�gʅ�^Sn���Q!:5�Z�iZCԈ:���3qg�%D��ݪ{U�3�tZ�`��u%w:�ZQ:Q���W f�훿9Jpl�)�3x�v���K7�b#�����X+J�(��h��P*Ӂ���Λ��!ה�ŏSL�h*'���\npB��ڪ�gNʝ�8BuҪ���Ό��8ni�I�s�US�I��;vvڳU�sR�7N�u�8�H|���ӷ�̎��8�q����+'���`�x�9R�	ծ��MaR8�x�)��'!���;�U��Y֓��sNI�g:�KT�y�3�g��Y����k���ܳn'LO(��3�w4�4������l���J����w��9�\\����hf(�_~���}9N���\0���b\"�Y餃Th,ڞ�@��D���\$�I��;�e��U��n����,�O��	X��g�-���+>ti'G����l�%\0�8�VB�U1�ye�\0KT�4���m��V2)\r]I/\rF���X���ߨ�a��G�¹�*�����>ER������Z�-)I\$����:�a�\0�Fyba�g�w��(�_@�v}�i�ʳ�S^�25DԳ�	��URO��JH��\\�is�f��K�N��qi�Sg�O\n�F~|���*@gR�_Q<9sܬ3i+ؗ�.Cw���|���y�6a�O�Y9���ɖ\n�Խ-([���_�}�S�]c�S=��������Y��U->�<���\n<�sO�Q4F�^}\0007u�k(/���/5{L�9�\0����&��[<���s�\0&��#�@h��3�V}��H���*�w+]'D�&�@�ց])��;TGe3��\\��n����d\$:�uN4�ykt�-dR!7����e4(P!��-��9�4�_PMGb��ıw����6O�S�F���)��yh0+����qT|��+u���+��A�?��	�T�3.q��41T��e��\n:P����{T�\n��h?��T�A�S��*���+�u�>�\\�Z����Y췢wEJ��%��s�L��d��y�+\rC�ߡ'A�l,�y�3���͗`�	_*�P� ThKDV���~5	�0�+�,�-?�]���3�֍K�`�^���I42(]�w�.�r����]�\nYƨB����	��}ЋR ��g�}:H��J�WP��\"޵���V\\�<��? >�����ܬ݆�=��:�\n0��\\+�S���f�U���U,�WCֈ�On��΅��.�e9|R�I'�[�/������2���Q��Bn:�I�\n��g�9�\r�,�R6����Q\$X�+�>����`\n�)/_8Qi�����=��v?5v�\0 \n���LG�Dm�w\\�F֌�Ѣ���dꟵ}s�\"��Yv�|�J*�9h���@XEU�*�(oQ]\$�B��,�����KT�v�AptCɃ\n�C,/�<��ڙEW�-V�P��=W�*%K�-Q`9	(��59Ӏ�m)�X��@�2���T@��\nS���bd�Eδa�+�DX��|U�	�	��F� 2�%5\nj�m��W�+�x�K��V�3#��CT�ek���&�,�l�jbd7)ӓ\"\n+�P��b��I�@�3��ܵjU��Es��)D�f뒃������P�Z3AΌ�\nwTh𗲪ۘ�4Z��<�uߩ�dq�ˊu(���bKG����n�Tﮈ]z��f%#�3I�fS��&}�@D�@++��A�h���\n��U�ޥ|B�;��Um��U�E�N�!�x2�1�\0�GmvH~��H�T�)�W��YN�\"�k5��vT#=�ڥ�<\n}�#R3Y�H�R�Iͳܦ;��Rl�1l�uB%TQJ�*���'�E�0i�dw,�z�ͥ:\$��;�?���j��)��)ԏ�\$32J}�&�[�\$��́�;Dn��E״�+0�aZ{���C ���(��:����O@h��D��\0��`PTou����F�\rQv����o�ܡ\$S��+��#7��Izr�pk�DW��Fs�9��Q� ���1�g��#�\0\\L�\$��3�g�X�y�y �-3h����!�nX��]+��	ɝ�c\0�\0�b��\0\r���-{�\0�Q(�Q�\$s�0���m(�[Ru�V����>��+�J[�6����J\0֗�\\���,��K�3�.�]a_\0R�J Ɨ`�^ԶClR�IK��\n�\$�nŏ���Kj��\n����~/��mn�].�`��ij��#K��f:`\0�錀6�7K▨zc��\0����/K���/�d���FE\0aL���dZ`�J�S��ʙ�2��4�@/�(��L��0�`�ĩ��_�L��]4Zh�Щ�SD�M��4:c��SR��M�E4�i��SG�EMj��4zd�թ�SFKL��%4�e��%\$�lKM2��1�ڔ�i����MV��.�ڔ�i����Lz�/���ۣӄ��M�,`�_��imS��gMƜ�jg�����5�9.��9j_��S���.��9�_���S���.�7�r�)��%�[2�m8�uT��S��3M:�]3�q���nӱ�KN�1|^�kt�\"��H�gKj�-;zc�i�Ӛ����\r<�_�-i�Ӹ��\"֞U.���i�RڑkOF��=:\\��\$Zө�MLE�5�x����ӻ_\"֜=<\0�t��S�9OҞ�1�~��i�����O��>�~q�)�F����=6:~���J���P:��=��T�)�ƫ��PJ8�@�w�����*��O�5]>��t���T\n��!\"��6Y	)��H�/P���3�	���/��P~���	�Ӯ�!\"��C����j� �eNJ������*%�4�1Q��CZ�Q�jTB�Q.�\rE)\0004��\$�2�SM+�<j�t�j0�,�9Q��}F\0\$�s��Ta��KΣ]Ecj*�'K�M��MGx��R�T1�#QꡥG��5�:�z�L��4u6z��\"j\"T�KuN֣�G�g\$jFSܨ�Q2��H��\"�MT��%R��Hz��\$�,�w�Re.\$r�z�)��Ԧ�-Q���J���ʪ@԰�=R&/�Iʕ1�*]T���7���Q��D&өqN�_(�q�c[Tw�QR�崜J�\0n��T���.��956c�܌�Sz�H���7�R�}�Sr8�N���\"b�T��Q�5MN���#����ES§-H��7\"�T��_S�}G�̕?*yԩ��S�P*�5#���܍�T:�]Pʟ�C*�ԉ�T:�-K8�5C����R�--MȾ�H��� �'T���H���H���ы�T���R���,���܋GTک-SJ��M*�ԩ�UTکmMH��M���>�gSD�5M�R���H�wU\"��K8��R���ڌ�U*�-U*��n¾T�IR�,t�Z���Y�IUF�51���W)v�k�_KƫpJ�5Zj�ů�R�4r\n�^jI�CK����}Uʓ_��ԛ��O�=N�R*�F-��R��%W���c��\\�aV>�EYj��d���ëUά�WX�5*�Ջ��Uy��Z��1k�ը�7V��R\\H�5h*�U���UƧM[���k�vո�3V�}[(�5W�zո�iB�O��1��T���V�;�[��pR�Gu�;T@0>\0��/I���W`�]��\0���8��P��]��1m*��ǍyUz�mW��|�ݓ[��֯�]J�ш��U������Z*�5\\j����Z��`Z�5~��E�W��4Z��5h�Q�^�cXZ��S��1o�V��U&��T��5}cU^��X��dm*���kUu��SfG=[��j�sտ��X�Kc\n�iR�H�i#��uWt��������X�cĹ��U���rڢ�UZ�Շ�NE���X���4��ud�E�eV^��K��n��V8�sX¥�f��/�hJ�-J]ӂ������zO��<Eh�\$勓���\0K��<bw��>���N�\")]b�	�+z�.cS.�iF�	���QNQ���V*������O[X�nx��P	k��oN��}<aO�Iߓ�h���T;�r񉉤�VD6Q�;z�]j�~'�:�[Iv��7^ʑ����j�w[������ņ�:u �Ds#���\\w�<n|*�h�m�Kv;Y҈��3�]��^#�Z�j�gy�jħY,�%;3������.�W\"��\$�3>gڜ���Ϧ�V�T�Zj�hY�j�kD*!�h&Xz�i���+GV��\"��Z�:Ҥ�+�NoG�Zjj�i�]ʞkO�_�֬ԐmjI����t��#�[�j\rn�����n��Z�_,���g�Ě�:���9����[L2�W=T��0��f�\0P�U6\ns%7isY�?��u�3���nb5�����X|G~l�&�k���M��������y�S��)�]�ܭr��ٸ�������?�}u'n0W-ι��b��Ǫ���k?�vQ�7��}p\n�����ٮZ*�9)��5ޕZW�-ZB���:��㫊W�\0WZfp�Gp���ٮ:�Fp����U��SN/��\\��%s9�S{� �8��Z�as�ۓ�+�N^��9�M�{�P5�� �Q���J���y����;����z����Y�V �3�:�D�I���+����19M;�������V���\rQ{��ծ���+��F�CLĹ�N���Ԉ�\\��)\$i���N'\0���P����]X�^�s1�f�&�\"'<O���̡�L\0�\"�@���%�6��UA�1�i(z��݁�\r�Ղ��bZ��+IQO�3���\r=*ĉ��)�!����`��h��,ЫmGPC��A��ٲ�A��(ZŰ%�t�,h/���i��k���XEJ6�ID�Ȭ\"�\n�aU- ��\nv�y��_���ګ�k	a�B<�V�D�/P���a��)9L�(Z��8�vvù�k	�o�ZXk���|�&�.�東C�����`�1�]7&ę+�H�CBcX�B7xX�|1��0��a�6��ubpJLǅ�(���mbl�8I�*R��@tk0�����xX���;�� al]4s�t��Ū�0�c�'��l�`8M�8����D4w`p?@706g̈~K�\r�� �P���bh�\"&��\n�q�PD����\$�(�0QP<�����Q�!X��x��5���R�`w/2�2#���� `���1�/�܁\r���:²����B7�V7Z��gMY�H3� ��b�	Z��J���G�w�gl�^�-�R-!�l�7̲L��ư<1 �QC/ղh��)�W�6C	�*d��6]VK!m����05G\$�R��4��=Cw&[��YP��dɚ�')VK,�5e�\r���K+�1�X)b�e)��uF2A#E�&g~�e�y�fp5�lYl�Ԝ5�����\n�m}`�(�M �Pl9Y��f����]�Vl-4�é����>`��/��fPE�i�\0k�v�\0�fhS0�&�¦lͼ�#fu���5	i%�:Fd��9��؀G<�	{�}��s[7\0�Ξ3�ft:+.Ȕ�p�>�ձ�@!Pas6q,���1bǬŋ�ZK���-��ar`�?RxX�鑡�V���#Ĥ�z�; �D���H��1��6D`��Y�`�R�P֋>-�!\$�����~π���`>���h�0�1����&\0�h���I�wl�Z�\$�\\\r��8�~,�\n�o_��B2D����a1��ǩ�=�v<�kF�p`�`�kBF�6� ����h��T T֎�	�@?dr�剀J�H@1�G�dn��w���%��JG��0b�Tf]m(�k�qg\\�������ш3vk'�^d��AX��~�W�Vs�*�ʱ�d��M����@?���}�6\\��m9<��i�ݧ��Ԭh�^s}�-�[K�s�q�b��-��OORm8\$�yw��##��@❷\0��ؤ 5F7����X\n��|J�/-S�W!f�� 0�,w��D4١RU�T������ZX�=�`�W\$@�ԥ(�XG��Ҋ��a>�*�Y���\n��\n��!�[mj���0,mu�W@ FX������=��(���b��<!\n\"��83�'��(R��\n>��@�W�r!L�H�k�\r�E\nW��\r��'FH�\$�����m���=�ۥ{LY��&���_\0����#�䔀[�9\0�\"��@8�iK���0�l���p\ng��'qbF��y�c�l@9�(#JU�ݲ�{io���.{�ͳ4�V́�VnF�x���z� Q�ޞ\$kSa~ʨ0s@���%�y@��5H��N�ͦ�@�x�#	ܫ /\\��?<hڂ���I�T��:�3�\n%��");
    } else {
        header("Content-Type: image/gif");
        switch ($_GET["file"]) {
            case"plus.gif":
                echo "GIF89a\0\0�\0001���\0\0����\0\0\0!�\0\0\0,\0\0\0\0\0\0!�����M��*)�o��) q��e���#��L�\0;";
                break;
            case"cross.gif":
                echo "GIF89a\0\0�\0001���\0\0����\0\0\0!�\0\0\0,\0\0\0\0\0\0#�����#\na�Fo~y�.�_wa��1�J�G�L�6]\0\0;";
                break;
            case"up.gif":
                echo "GIF89a\0\0�\0001���\0\0����\0\0\0!�\0\0\0,\0\0\0\0\0\0 �����MQN\n�}��a8�y�aŶ�\0��\0;";
                break;
            case"down.gif":
                echo "GIF89a\0\0�\0001���\0\0����\0\0\0!�\0\0\0,\0\0\0\0\0\0 �����M��*)�[W�\\��L&ٜƶ�\0��\0;";
                break;
            case"arrow.gif":
                echo "GIF89a\0\n\0�\0\0������!�\0\0\0,\0\0\0\0\0\n\0\0�i������Ӳ޻\0\0;";
                break;
        }
    }
    exit;
}
if ($_GET["script"] == "version") {
    $jd = file_open_lock(get_temp_dir() . "/adminer.version");
    if ($jd) {
        file_write_unlock($jd, serialize(["signature" => $_POST["signature"], "version" => $_POST["version"]]));
    }
    exit;
}
global $b, $g, $m, $fc, $nc, $xc, $n, $ld, $rd, $ba, $Qd, $x, $ca, $me, $nf, $Yf, $Dh, $wd, $ki, $qi, $zi, $Fi, $ia;
if (!$_SERVER["REQUEST_URI"]) {
    $_SERVER["REQUEST_URI"] = $_SERVER["ORIG_PATH_INFO"];
}
if (!strpos($_SERVER["REQUEST_URI"], '?') && $_SERVER["QUERY_STRING"] != "") {
    $_SERVER["REQUEST_URI"] .= "?$_SERVER[QUERY_STRING]";
}
if ($_SERVER["HTTP_X_FORWARDED_PREFIX"]) {
    $_SERVER["REQUEST_URI"] = $_SERVER["HTTP_X_FORWARDED_PREFIX"] . $_SERVER["REQUEST_URI"];
}
$ba = ($_SERVER["HTTPS"] && strcasecmp($_SERVER["HTTPS"], "off")) || ini_bool("session.cookie_secure");
@ini_set("session.use_trans_sid", false);
if (!defined("SID")) {
    session_cache_limiter("");
    session_name("adminer_sid");
    $Lf = [0, preg_replace('~\?.*~', '', $_SERVER["REQUEST_URI"]), "", $ba];
    if (version_compare(PHP_VERSION, '5.2.0') >= 0) {
        $Lf[] = true;
    }
    call_user_func_array('session_set_cookie_params', $Lf);
    session_start();
}
remove_slashes([&$_GET, &$_POST, &$_COOKIE], $Wc);
if (get_magic_quotes_runtime()) {
    set_magic_quotes_runtime(false);
}
@set_time_limit(0);
@ini_set("zend.ze1_compatibility_mode", false);
@ini_set("precision", 15);
$me = ['en' => 'English', 'ar' => 'العربية', 'bg' => 'Български', 'bn' => 'বাংলা', 'bs' => 'Bosanski', 'ca' => 'Català', 'cs' => 'Čeština', 'da' => 'Dansk', 'de' => 'Deutsch', 'el' => 'Ελληνικά', 'es' => 'Español', 'et' => 'Eesti', 'fa' => 'فارسی', 'fi' => 'Suomi', 'fr' => 'Français', 'gl' => 'Galego', 'he' => 'עברית', 'hu' => 'Magyar', 'id' => 'Bahasa Indonesia', 'it' => 'Italiano', 'ja' => '日本語', 'ko' => '한국어', 'lt' => 'Lietuvių', 'ms' => 'Bahasa Melayu', 'nl' => 'Nederlands', 'no' => 'Norsk', 'pl' => 'Polski', 'pt' => 'Português', 'pt-br' => 'Português (Brazil)', 'ro' => 'Limba Română', 'ru' => 'Русский', 'sk' => 'Slovenčina', 'sl' => 'Slovenski', 'sr' => 'Српски', 'ta' => 'த‌மிழ்', 'th' => 'ภาษาไทย', 'tr' => 'Türkçe', 'uk' => 'Українська', 'vi' => 'Tiếng Việt', 'zh' => '简体中文', 'zh-tw' => '繁體中文',];
function get_lang()
{
    global $ca;

    return $ca;
}

function lang($u, $ef = null)
{
    if (is_string($u)) {
        $bg = array_search($u, get_translations("en"));
        if ($bg !== false) {
            $u = $bg;
        }
    }
    global $ca, $qi;
    $pi = ($qi[$u] ? $qi[$u] : $u);
    if (is_array($pi)) {
        $bg = ($ef == 1 ? 0 : ($ca == 'cs' || $ca == 'sk' ? ($ef && $ef < 5 ? 1 : 2) : ($ca == 'fr' ? (!$ef ? 0 : 1) : ($ca == 'pl' ? ($ef % 10 > 1 && $ef % 10 < 5 && $ef / 10 % 10 != 1 ? 1 : 2) : ($ca == 'sl' ? ($ef % 100 == 1 ? 0 : ($ef % 100 == 2 ? 1 : ($ef % 100 == 3 || $ef % 100 == 4 ? 2 : 3))) : ($ca == 'lt' ? ($ef % 10 == 1 && $ef % 100 != 11 ? 0 : ($ef % 10 > 1 && $ef / 10 % 10 != 1 ? 1 : 2)) : ($ca == 'bs' || $ca == 'ru' || $ca == 'sr' || $ca == 'uk' ? ($ef % 10 == 1 && $ef % 100 != 11 ? 0 : ($ef % 10 > 1 && $ef % 10 < 5 && $ef / 10 % 10 != 1 ? 1 : 2)) : 1)))))));
        $pi = $pi[$bg];
    }
    $Fa = func_get_args();
    array_shift($Fa);
    $gd = str_replace("%d", "%s", $pi);
    if ($gd != $pi) {
        $Fa[0] = format_number($ef);
    }

    return
        vsprintf($gd, $Fa);
}

function switch_lang()
{
    global $ca, $me;
    echo "<form action='' method='post'>\n<div id='lang'>", lang(19) . ": " . html_select("lang", $me, $ca, "this.form.submit();"), " <input type='submit' value='" . lang(20) . "' class='hidden'>\n", "<input type='hidden' name='token' value='" . get_token() . "'>\n";
    echo "</div>\n</form>\n";
}

if (isset($_POST["lang"]) && verify_token()) {
    cookie("adminer_lang", $_POST["lang"]);
    $_SESSION["lang"] = $_POST["lang"];
    $_SESSION["translations"] = [];
    redirect(remove_from_uri());
}
$ca = "en";
if (isset($me[$_COOKIE["adminer_lang"]])) {
    cookie("adminer_lang", $_COOKIE["adminer_lang"]);
    $ca = $_COOKIE["adminer_lang"];
} elseif (isset($me[$_SESSION["lang"]])) {
    $ca = $_SESSION["lang"];
} else {
    $va = [];
    preg_match_all('~([-a-z]+)(;q=([0-9.]+))?~', str_replace("_", "-", strtolower($_SERVER["HTTP_ACCEPT_LANGUAGE"])), $Ce, PREG_SET_ORDER);
    foreach ($Ce
             as $B) {
        $va[$B[1]] = (isset($B[3]) ? $B[3] : 1);
    }
    arsort($va);
    foreach ($va
             as $y => $rg) {
        if (isset($me[$y])) {
            $ca = $y;
            break;
        }
        $y = preg_replace('~-.*~', '', $y);
        if (!isset($va[$y]) && isset($me[$y])) {
            $ca = $y;
            break;
        }
    }
}
$qi = $_SESSION["translations"];
if ($_SESSION["translations_version"] != 131114700) {
    $qi = [];
    $_SESSION["translations_version"] = 131114700;
}
function get_translations($le)
{
    switch ($le) {
        case"en":
            $f = "A9D�y�@s:�G�(�ff�����	��:�S���a2\"1�..L'�I��m�#�s,�K��OP#I�@%9��i4�o2ύ���,9�%�P�b2��a��r\n2�NC�(�r4��1C`(�:Eb�9A�i:�&㙔�y��F��Y��\r�\n� 8Z�S=\$A����`�=�܌���0�\n��dF�	��n:Zΰ)��Q���mw����O��mfpQ�΂��q��a�į�#q��w7S�X3������o�\n>Z�M�zi��s;�̒��_�:���#|@�46��:�\r-z|�(j*���0�:-h��/̸�8)+r^1/Л�η,�ZӈKX�9,�p�:>#���(�6�qC���I�|��Ȣ,�(y �,	%b{�ʢ���9B��)B����+�1>�P޵\r���6��2��L�P�2\r�\\*�Jb�=m��1�jH��O\$�����4 �jF�o���F4 #0z\r��8a�^��\\�N-�����|�єp�2��\r�:x7�<�ص��^0��#�2�jk6��@��������ΎA&2��u�\n�1��lĠ+��s�	���<��M�]l�&!��b_2���Oz\r��a7���1�7�����i��\r�ӊv�è�b����3����c2�N1�\0S�<���=�PȤϭc��%����������_�accC����\n\"`@�_�d�7�(��[V�n�6��9��h8�k��/k˯K,�)�+Z\"��󰌻�����\"MF�����'iʌB\r��0�6NRL�D�B�ލMp򍯖t�F��^s1�t�!ͺ\n�p�7}K��`O-�d��>O��6t��P�c�_W�6W�P���pҐ�b��#2�x�#�\"�2�I]xP���L��tZP*1n�}\\گ�7ԫ� �@�}�I1T�.�A�E\r��W�a(�2�Ài !�H��x���u%�����Uj\\�(��W�*�S\niN)�@��\"rT��T�����r�Vm�0h�� F��7�0���=��4�\n��zw=G�����>nU�'�xvT12^	'؍	a�m�;�ET*�{-�0�F��Pia������Ky�HG����MA\0P	A�8�O�A@\$���'�I�(^��߱���L�n\"�T��b|h91&pt���\rlp6�6bTK	q0\r�m\n��Da\$%8ᐟ��M`�>ǌ��H�0I\"�rC(K��?怀�fTI!�&�F�����d��W��ׁi�!<)�F�^�R&�8�c�����lS��\$�INprd\$��0cIC�ig0̑�@�C�\nm\$� �&H�0T��9p�#z^&�3 �H�5lZN��I�(�*�BxNT(@�(\n� �\"P�i�`^���F�N��;B�\\ӄČ��O,le	���p	Ho5Dr����v\0T�V�6R�w�;�};����T�1�0g�R1��c`lH�c���'�@������?Bh<��O��wP���0��D��r#Gtt���<VL�VB@C�%^��+�SEb���Fa��%�MN-s��(����[hyD쐭u��Jص�x�����Z�-w9�2\n��������	�'��2�P�Z-r�����*��D7�����^�\r�/!]�¢S��4�!��^�� ?7z��@��@ �����o��~=��\"��x��6]��B`ú���	�f��z�v�'�� ��A\"q�Ο0����!��k��Y��A�JC�̼�\r��߈r�Ș�#�]���/¨/e�j]rյIIĦ�&Q	���w8�:G�.�M�0�6�rJ`(+�P�o�'�)1m��pHGd�Œa4�l�Xz�\0����9eg�*j@ËhI3��œ��vYyȔ�������X��U�[�ɻ/�����PCL*lmo�rԔ�5�lp�\\f3cو����,!aL2S�{r����+�˺��TK(�v@}�a�z��ğV���x氧��*�\r�d3��In�͢�-�\\C%s�u���m������M�]m��o�2��֞=�+��/����>?�8�4l�ۘM��Κ�'��iE0��/�N]d4bdMrA���&,X:B�\0�\\^oꎠO�N�}[?^+����ɮ���ҀAJ�g)�\\	�vn�y{����N����K��)��{��]67�k �w�d0�Q��<�P˧�bI�#���:����R�͋�\n�f�OIt���>�y��+ok�������e����9I��_i�;+�ܟ����+���k|`��Wy��)���x��7�흉wV�e�x!�~\$�j<	y���2����9Ȕ�I�`/������N����h�����NT�BP�%�t&.��� �O��Z�Dg��o�20&�fV�@�!#�,�b,&����6J0R���e�����n�pi���v��j�0X�e����#�����r�н�V���	�*�o	��\n��	0�J�8D!\$�\n�F���\n�y\n/��p�N{\0n�\r�/�%\0�<D\0K��\0��E��@�zCN]�<�P\rbL\"�}�O��7\r�N���<7�Lu��.\"�	q%�Րꌛ�t�m�N��o�d \r�V�\0�`��eܢ0sz\r �sb�A\"p�h�@�\n���pG�\\.��̓Z��L�L��-�,7\r�|'0p�x�E�o��qz��إ�&6��\r�.�־�0V����4prJV����\$fKC��\n\n�'Pab�ڂ�����%�b��r�R�m�����R5�\"�#/�\nR%2�&�6\$⸍�#�8��o+��/Nuq�C/�L�N�@��vd��v��y'L����0�O`�%�t�rRZ\"�\"vl�L=�	�,hÓcT3�eJ�L2��@�,�P6oJ�f���\"�-LDD�]����\$��\"���/)[\$\r�/�\\";
            break;
        case"ar":
            $f = "�C�P���l*�\r�,&\n�A���(J.��0Se\\�\r��b�@�0�,\nQ,l)���µ���A��j_1�C�M��e��S�\ng@�Og���X�DM�)��0��cA��n8�e*y#au4�� �Ir*;rS�U�dJ	}���*z�U�@��X;ai1l(n������[�y�d�u'c(��oF����e3�Nb���p2N�S��ӳ:LZ�z�P�\\b�u�.�[�Q`u	!��Jy��&2��(gT��SњM�x�5g5�K�K�¦����0ʀ(�7\rm8�7(�9\r�f\"7N�9�� ��4�x荶��x�;�#\"�������2ɰW\"J\nB��'hk�ūb�Di�\\@���p���yf���9����V�?�TXW���F��{�3)\"�W9�|��eRhU��Ҫ�1��P�>���\"o{�\"7�^��pL\n7OM*�O��<7cp�4��Rfl�N��SJ��\\E��V�J�+�#��܇Jr� �>�J��(ꆶ\$(�R�M��v�GI������ťr��Wj�|�\"v���< ��k��(���3\r��1�T[�nڰh�����޳�����\0�2�\0yw���3��:����x�\r�i�PH���p_�p�B�J`|6�-+�3A#kuF\r��^0��zC�ܪ�����s��j�Q8������u,15���XrZTƖ��n�\"@P�0�Cs�3��(�Z(�f���\$������:��Yk���U��<���:����0�����ŋ�l�SR����i�Z��)�v�kR<�J�#[�q77WSI�Y<ь�l�MT���K���#oci@�c�7S����b���!�jh�;[3��!{�cT����\\!>6}�TT�o�1lk�Ȧg�[���H��rǙ`yٍr�1��a��]�7��(v��p�ý6�+���q�yj͗�g<�� Bld5�=����r���@�\r(o��6-3\n~3�X��� y�dA�<�\0ꢃ��\\!��\0��9�k�:(ZC8a=@�!��@��pu7`�9���I�h�)� �A\n�\$��\n�\\��j&Hh���˒Ko!��+4J��\$(����x�YtrHJ�Ɨ���9�@͐(\\��8 Z!/PƣTXdZ��Я���C�\"�I@F�əAOe�C��\\N�v0��g��Ip� �]X��@�aAY-\$���n�3b�����W�\"^��K/���X`�݄��܁�sbIC���X�	!�8���C����#�U��ͬG!��!�\r\n�C*.�@ʒ����j�F����bL h4�zO(�@��c�F�8 i9��a�C&+\r�7�(u�iB��a���1̙�[�sG����U�a|+n����̍Hh���C`%7\$��!S��L����O.9�j�a�6����\$l��]�D7�z�S�Le��eł��j�&¥G��+�~+m��G�^�(e��#��V\\_��i�O?�j|&a\$��>�CK[[m��|l��qo���ddT�Z�����n�)=aC��G��ΰ�\\�\n<)�BD^tm��)B��ڑm�gQ-�G�<��f���y&&�N�P��[�~��3�\0��v���v�i�.�&\0�V��]�*���i��qu�kOg��AG�㣢�+e�Z!96,n����A<'\0� A\n�\r�ЈB`E�j~:��N�b�D�ҥ)�K\0/��g([S0�P.͒��|\\��.)1H<���8�1ID�)�rA�/B�Ԣ	\\J\rs����_�\\:Xe�m�\"56��NW�O5�@w�ẃ����,G�\r� \nj����g��5J~(�`aR\\Bz,VjNƗ+�\"^m'wDS[yo:|s��bˆ�\\�c�gi\$��ƆM�Ñ!�i�C\0P \0S\r!�����S���UǆH�Cၢ޷E��e�@���ƶkC(w&����g�v1��������w�ܻ����I)R��zMc�SNIJ]�̜a6�u�G����w��'S���3��MVqʄ�@V)��GϦ�.P�gk范�?�D�>����ң0ʕg�@�BH�i�H1X��p���'\$�?�|��`F�����F���5��{޺��FU&���,#D ����/X����A�f-�bPk��/-�!6���.F<��)�a������e6t\\R�ro!������U*�au���-�;�1��Hމ\n	����G���\$�yzU�����ǕA	mH<yS���\$Nt�,-�uU<3�	O��h�+,5\"�;��:�X�VA h�9B���؟���\r��ؗ�	�x[���Frҋ�d�	�4�I�?\$�.G��~�F������E�b��'�i�����p�	\0�N�)N��^�0F��\"lg�R#��Gv�\"�-�lMf�A\n�)�~���T��.�g�,�ďmՐ8K�`��3?G�����/�l��I����ʘ/�P:�n @RSPi\0���Ϭ�����iLC�g�Wjr#��|��l�ˏ���\n��}�0vh�\r���P�GP\0�j��p�{Pz��\0�xzp|��2��EB&\$'\\������¶pB^��e���+�1��nz�l��o���\$���m�o�&�L߱Q���D8���V���\"\r�Pݪ�E\r�1�#��	f�&�g#�8�ft�^�����b ~�P~܎�r��l�m�L0�y��(�ip�!q�M����Q�l\$N�a�\\ߑr��\n�?/��-�W������&1O�hl�B���\"qd�h��.�Qf{�\$��*��ZqS\"��\$�2�@�RJ���g�T�P�\$j9Fv\"o�FȔ�\\T�x�� ���\"�o����L���\$+1(�O&r\"㒧�5%�+*�\"#��V/��2R}+c�G2�-r]-���\"ɐ����M��F2�eĔ?�+R�/�02*�/���0 �-�o-���E,I���re�I����BU2���<H�3�P�S3\"��Ow3��3S4S4s�nJ�z2H\"me/p� eH1*�X�\$�8�_#eQ9e:���y�G+î���mR|>l��d[)�-\rGFl��1���Җ��V�¨�l�}=n<'<O�:&���8��=�hz\r�Vh:`�P�]��o�x�@����Z]bf��\r�관@�h�@��\n���pO�B�����ov��*�x\$n����f�@�B�.-�wf�fqU!RwBze�|`E\0�D������UC�� �\r��c��\\���T�E��.�R�e4�+>�1��r�Fg���ǯ��-��ȣ���t��O��P�2�,p\n\n�6cD4�B�Z\r���Ť��z.T�tø�ļ+&�M�b��P��Q� &���t�B�U���2ʈ����\\�ZU��[������:�^�d�8�lu0�:O*�4�ɔ�1�|��而�<��L�Ȍ�>\\SR�Օ�`*���/����%D�	\0�@�	�t\n`�";
            break;
        case"bg":
            $f = "�P�\r�E�@4�!Awh�Z(&��~\n��fa��N�`���D��4���\"�]4\r;Ae2��a�������.a���rp��@ד�|.W.X4��FP�����\$�hR�s���}@�Зp�Д�B�4�sE�΢7f�&E�,��i�X\nFC1��l7c��MEo)_G����_<�Gӭ}���,k놊qPX�}F�+9���7i��Z贚i�Q��_a���Z��*�n^���S��9���Y�V��~�]�X\\R�6���}�j�}	�l�4�v��=��3	�\0�@D|�¤���[�����^]#�s.�3d\0*��X�7��p@2�C��9(� �:#�9��\0�7���A����8\\z8Fc�������m X���4�;��r�'HS���2�6A>�¦�6��5	�ܸ�kJ��&�j�\"K������9�{.��-�^�:�*U?�+*>S�3z>J&SK�&���hR����&�:��ɒ>I�J���L�H�H�����Eq8�ZV��s[����2�Ø�7ث��έj��/t��Z��.��O��m�5�cCmҨL�X#�ĳ8��Q��B�ŤC*5\\ �ʰ��2\r�H�F��uG��#���pφF�|cƣ��:\rx��!�9��D�d#@�2���D4���9�Ax^;�pÀ`Q@]��}��y(�2��\r�\\k��X���px�!��n9)�-	;�%��^\r��jʣ��]U8{ā�����{v��M;��@O;D�Kb��Ur�\n��7`C:<��kT��`O)�(3J>M+�{��PH�htT�4�� �S�P3	��8�i�q~���c+3��C%~#���po	ܚ���8+����yqj�L\"�=��w���V�H�y��4�G���(:ں,�yޭ\"��#���w��DX\nA�Re�+��n@ދn{%4��׉Je;�d�&�yVq�AL(���!)?FL�A.���Pǹ��f�x!��B�p���ۡp�n+�\n�%���[{Z��qa�`9�V�H����!��w���t��4�H�4(L	\$\\x��/Wsaoƥܸ%b�iTA!DȔ��4&l6@��N��;�l���/K�~�%�È\0S4\r4�h�<S�H�Q&*��3�Ӓ|ȡ��d4�/I����s�0�x��8 sf���2��LWQ0A��Y&W��y�\nM�=\nͩ�]��zʉ>��Y�\$��W��i,���KDP�Ұ�O*�|��2��wK�.�8���	)������&�<L�XK�C��t��h�*x��=\"P��R]ؖ�mI9�(�P4B���rՈ�5��XrI�d,`��@ eL���D����E����_�+t9��\"c!��\r)i1'JI`T�b�@�S��O��oR�'#��PefwR�V�Y{1fl՛��wH�@hA��,%���CIih5��~�5��i�)�T44�>#χw4�\\O&m�U�ڽ^<c{*��U{N�l����Wѷ*u���(�Xm3Y\0����5������CfXth9�V�0u��7�vk�hq�;l+�ẙ���a�9��g�\$`C� p�/��lŢ�\n��.�@P��>r���i\0(-�����A	*(�\rd00�Lؕ�\r�9��U�g��!#�d��{ �Ȓ���8M��x� �.��`z��E%DR��������Z�祠:J&���;��K.�='�7q�	�Mn���i�R��2���Sº��;���e2�-�g�����<U'�2�h�?abї�gA��N4��/��I%��>@\$+�HOD���\\lZ�%���H����Xb�M�	�~�Iv�R��Iy�ŧ%R�6ǎк�&��,�\\�XA�����hq�\0F\n��.ɂK�\\b~椥�z��Pkg9��b��n���r�L��,j�H�bB\n+����E��=�cV]\\��}*ճF8�N�h	��b��;�Ո�,\r��;V '��w�]C��GpY����G�	ߒ����\rz����d,_���\n%}/J�7s��C��k?ɶ�f{1���>iǹ�U�3!:��ٮOZG:��8`;ؐV2��nr��/4��JXl�/��y{OuR�G��\0\$b'�B����노��c��2�s�A2�\r��J��<�[x��Vs��=ڿp!�h������!�񢣈{��<� 	��RPn���cd�#d��8�������؆\r�v�a]*B��7���m#�a~��Q������\n��\"�y�!=]��^�F�D��Wk��3v.�,���txH>�:�\\����5g�FByɼ������/��t�5@�\n�� �	\0@ �E\0�G&el0G�za@�Xd�T��%����_��s�6�@#*8�hsPFQ(F��k*�i�J)�'\"�I�7�x�D+<��qE+�Z]��By�N6G�N���l��9�؄����]��#���l���I���'�q~{L�P�+���/q���m�Qo��K\r��mT�o�-�p��jzP�Ë��	pn5,�Ρ|.!</���[Nt%�6L��M����%��ώ֑DZ	�yi�3� �H����H�\$�	�c�zՂ>��;M�3���-V��(Ɔ�@�Br�g�@Bj�辗�����|�M�\"b�B��up]B��������|�j��P�NT��t{\"b�������+���XR�]b�.��9�oQ��#����Q�s��N���]\r��O2o��]��Mw¼(G�O)DJLn�%���\\8 �E@ூ/E���28�2j�(�A�4��D�E\$��\$�\$ȴ��lѬi������7GL�b�(H�R+�F,�Px��T��\0S#'�]H��-�R�- #�.2ІrԎNN�2Q,a�n�2��P]-r%q2��q\"��0=.��0�ʰΝ*R�l�7\"-n�-�̖���1�/\r��k1S*~�AsE2R�2����º�S\"mQ6\"�Ԩ`���x��&P�L#�%5	�;��6�\r7\rI!��7��7�a80Ip̰r+��\nN*�P;5S���%.��e;Ѓ�W*γ^ق��24S炣4��+mʾ�ۧbwD��� �B<(���|��^:��c@}Bl��Lȥ�8k:]��J�3�@fĄ�)���	~�e.���Nxw�R�<��=� ��3n��C\r��O��M���aD�ޏ���=S�!I#0�H�Q-�7<s���\0]��>�,Lŕ\"����`���t�tQK܁2���g<�cKg��t̡�323�Ij\$�t��)%J�>҄)�aG�x4�O'�K�{�3P���\"A/.P:ar7'���d��	�6K;e������	:3�<5<R3�T�d��1J�γ�P3�V�6Ա4�P/�Pqzh�?�_Gu~�R�m�o:H�O��4�	XG���4�'wYUKWt��I�����\$������BQN��JQ�5�J��\\�]%[]l�Q�W��\\��^lNx,�_�+�ڱՌ�M��k^M4�6���Y�97u�\\�bJ���I��\$I�Upx�3Nu�Z��I��X�Pu��6*lH8����vM`�AM���AJ��IUh!V{fϟg�_T�W��f�gϳZ���ci��d-�M���[��t�gS9dg�������g.�5��_��Ķ 6�4v�iq~�1��cYb� ���\$�)��Big7��@ud�\"�APyӟq��r�SY)wr5Y;�Q+�#pD��?u\$��.��	fQ��qV�.��\n�\r)�u�\nqT�s\"�	�us����b�:bb�\n�g�cu�8AV�VwBQ��ֶ�B�E�\0�\n���p�I���kп	3�m���IB~7m8T�w�4�h�}�]s�2�i4�%1mj��#� �V�m�>#�la^ş:��Yը�Ĥ+�N�\"tXXjrW�5��o�>���fQ%J�o�x��wD�-dT�Jy��n+ڃ(H�t�|�������� �՗1)¸ۨ�4��R���)���q�\$Y�4kX����|QZ�7=F�����4�dP� x��nI�eؓ�j�L��ؿ�8!D��J��|5C;5��r�W��X��\0A%��a���,m�-y	�H���?XS�d]��on�[�\$9N�tbHJn9og���Z�8��hu��O�XA�W�b�������I�576�P_H\rb�\n�#3�Ԏb�/\"/�6��>r[_�c'����/e�wM�U?W�)t��#�";
            break;
        case"bn":
            $f = "�S)\nt]\0_� 	XD)L��@�4l5���BQp�� 9��\n��\0��,��h�SE�0�b�a%�. �H�\0��.b��2n��D�e*�D��M���,OJÐ��v����х\$:IK��g5U4�L�	Nd!u>�&������a\\�@'Jx��S���4�P�D�����z�.S��E<�OS���kb�O�af�hb�\0�B���r��)����Q��W��E�{K��PP~�9\\��l*�_W	��7��ɼ� 4N�Q�� 8�'cI��g2��O9��d0�<�CA��:#ܺ�%3��5�!n�nJ�mk����,q���@ᭋ�(n+L�9�x���k�I��2�L\0I��#Vܦ�#`�������B��4��:�� �,X���2����,(_)��7*�\n�p���p@2�C��9.�#�\0�#��2\r��7���8M���:�c��2@�L�� �S6�\\4�Gʂ\0�/n:&�.Ht��ļ/��0��2�TgPEt̥L�,L5H����L��G��j�%���R�t����-I�04=XK�\$Gf�Jz��R\$�a`(�����+b0�Ȉ�@/r��M�X�v����N����7cH�~Q(L�\$��wKR���WF5\"�,ԕ�_-�eR��������S�8u*P��\nِÕ�8���XTAԩJ�����P�2\r�d�O��>�s�#��߲n��Nc���K��O��BPÐ��4C(��C@�:�t���9�}8M�8^2��}9д8^.A��7��`�7����7�x�`(gd�7Dz·+�/FQ�����1A8ՓI��iҮ\"�)E�/�)�T9tUԱM�/i�����78<��5�~�B��9\r�`ΐ��%=k�O���\n�@�ˢ����!kR{{J�T�L�84�sEq�\\�Ƕk��0�]u6`�Cb}��6Pʶ�Ûwnx�%C�����|d\r��d^�9U�s��@AaJ,�������0��⭶��0|�Tծr��1��)sn��,!�\$D*�*&\n#�p����\n�@l>kА0��HQ	��Jj�Vh��٢u��Bh1��r�gqp�n���baI����0txQ1&����1 �|MC	SH�LɎ+(T,���Y#y����%h�m��w�qE�% S�tHw�`���>�M��7�S�\${%:&Q��Ǆ�� B��A<E���s�D��� wN��<��3`�ϔK8İS1Iny�q��<�\0��Ji��\0��:Lm�:)�C8aI�����z�������&䐧)� ���Y�-�@��h�aEq+�h�B�N����S�s;@�Ԭ1���Y��\nf>N,9N��;�鱰5��C#?lG���w�>O�:=g\"��I�-�l�-�HW�tVxD8��!nPV��(��	�4��7 ���r�h�N	���S���Z�k�4�U�[Mm\r��6��܃�tn��6������_k��7\nChp=���J��x ��]�KTC[�PI�u�Z�m���C��duՅvRU���T�l�e8<�&���@�CdZG�8&�yi�a��?O'=\"���)����� s6�;\riCLj\$�:�bQ\n�4)Uq�;ROΘ \n ([�]\r9eQ��\$�{�`Ar4��6B�Y�Jc&,�5�eUiָ����{�+�P�:#�����\r����<�]�@�~MJ�L�hy]J&���,G��D&x.��Y�[ #+(c�H)�{�E�h]\\ͽk;f�%]2b3���R�M{��d6J�szcI'y�����|����6P���}ɸ6�&���|���;^|^�ς�Y	D�;�0/0O\naP�I�@Uh�Ó9\\�`�L�<� 9xE�Hh�sz�)E���VEV��(�F����\0��t�)^l^ؿ;�on�<1��� ؜ƴ����h4��ն֌�!�������j�+4D��Xu��2ee-�AԲ��p�_�n��ee�Ƹ+XGd��#/�\$PY%>��H2��iaH�����'m�9�/���P��2rŲ�S\n���x%E�!WZ���ۜ�SП_DeQ���@3VQ��?4U�LWG���c�34\n�ls�*':=U��Ty!��F��*т�f��DQL��95*/�QL��;Ax�2�4�R�u�:]�N�<�%�����q9V)��_!�r6|�猤�>�J�%�˖���g��7���]z�}�3JI\r!���CB`xS���<�z�T�s}Q��&d��5�����7���=��;���\"eǴ����~_���}~�� t�EK���ܔS���5s�K���\nlt8�2��0�%�\rZ���mh�'�ץ��R��\0��GP�o�y�L�����ԮE�@�ĸ���c0J.������d10)N��/rU�<�b�R0f�:��z�H�iz�Ɂ� �\n��`�\r����L`O���c�Q\0�Ǟ��L��v����b6{@^�:�z�{D\r�a���pTE\0Ä�(�t\nf�\n�.0�w�䕇�bv��B�����(��Y/����й����i\n�L��L�.��#b��xP.�p�����sM��2;P���(�p�:��D�D�*8�Vu��B�\r�AH�Q{�N�@�����JqT���������i`�ܭ�M��\$(-Z�Q,���T|0�x-�&N� ��ˀ��L��i�\nm*?r�ܣB�ǉs!w!L���%B�\0R.!�>/�`y�rx�\";,�nc!���X��2l�-��5P�\$p`7O66�V/B/%�`�F��\$p@�jw�v���c2�q�󧚑\n���즮�-Ωji�R���R#sG�1�ߣn�P1 ��Ei+�1+�w,R�q��B��Am+Q ��-�jw��*ϕ+�-r\r�g0	K�.v��/qK�k1R�)v� s%1*U*i0#ncj}PpR��`�H��1�*�j���#P�\"n�`�sR*����@��7M���l#34qn⯤Z���F���:,�1�Y)�8�)+����5hN���\0P�p�74K�R�Ҏ�V�\$�*���/T�r��?.��N�\\�,)R�n �\$+23�+qQ>O���37A;?��Γ)8�r��!;r�R���>��%0s�3S\r?r\0V2�/�5D� ��1'A�[/�-F)Ր�2�C�(�=E�-(�G�o5D.w��B��qXtm��=��,'�Gb�Atu�Iq[�o3����\$�<:���EKrO��K�CH�R,��>+�TT}�A�F���t[IWt����LE/����^�4�tN�V�f��pZrT�dC\n��I��K�-�>s\$�B�B�Lx��LH�3!!MT�/U�K(W\$�;:��Q�B!�Up}�\rI���!�(8�4��\$�hQ�\$��]�O-�O����W�Σw���儐ޓ�ʒ�b��']; �SS���u�� үPU!ORK6���]a57au�BU0Τ��'5�0��EuL1R-�oZC�nxu�b6B�}d�md�iBu�2u	/�Iceo_�=Q�ʕVa�5R�)��J4�C��L����&h[!��/�꥽G#���1\0�hR�)/)-IS�uQP�0�-A��lgA)6R;53f�EI2�\$�Qh�mL��P�,A�(�6�H{eeOi�F*&�j��G��n0���Swfv.�w\$�CWq\rUqno�s�]z(�cV��'�J+~ ��O�cP/mB�sR3,�2�u�aԯoWkaNṯ�u�QpPHVjLT�bw��W�ww�q�tWUxH�A\nCb��r5��ec�Sl�Yyv�Y{�-{t��7y��'��GB1^.oL�-u�w�/	%�\n��|�?VCv�@�e�R+p7�yYnX\r7��cAb�bW����_v�zw��]X}6�z�T��rVV��F(H�?l�c|XxY�5�z3g�3e��'�u��/n4ш(�}��7-���G�X^�#<���8�@��*�:�sNPU)�9}x!aQ���A�S��N�|U�VwoT�o�e5�*,�Qb�R!Js�Z��)Q�R{��)�_*�*B	�4�N��8� ����`ƛ��\r`@�ƴ�h?i�\r��\r ̝��.��ۀ������\n���Z��NI��s\r�����NF��k8�oc����\"Tݘ��|����iW�с�+/�C1����8�ƸUl̉dB�	�M�\0�QV��G��eH�Q��7�A~E�~����*�h�g����N`�2\\'��Çw1���G�p��j\0Zn����㐪N����/b6�o��A�xB�-#��5�`�z(Ë&㵙x�8S3��P6�y^F1y*Ӈ/�T�x2M�ۃy��8�7_`�a�D`�^��<#ǖ@��@�\n�g��Q:���@C�	a����p���\"\0�r�-�<(�R�3���/W��9�T)M�!��Z1�3؊\"�`G.�V:�QSM�N\nƒ��\r��8��D�X�����-�)<����ΙӃ�`)�`/d����_U��iN�5���W~�+�i�nT����6~TÈ��w8}Pgk����#VDV��h7w�	\0�@�	�t\n`�";
            break;
        case"bs":
            $f = "D0�\r����e��L�S���?	E�34S6MƨA��t7��p�tp@u9���x�N0���V\"d7����dp���؈�L�A�H�a)̅.�RL��	�p7���L�X\nFC1��l7AG���n7���(U�l�����b��eēѴ�>4����)�y��FY��\n,�΢A�f �-�����e3�Nw�|��H�\r�]�ŧ��43�X�ݣw��A!�D��6e�o7�Y>9���q�\$���iM�pV�tb�q\$�٤�\n%���LIT�k���)�乪��0�h���4	\n\n:�\n��:4P �;�c\"\\&��H�\ro�4����x��@��,�\nl�E��j�+)��\n���C�r�5����ү/�~����;.�����j�&�f)|0�B8�7����,����ŭZ��'��ģ���8�9�#|�	���=\r�����Q��9��l:���br����܀�\n@�F��,\n�hԣ4cS=,##�M���B�B�1�S��&��!�@43Ul\"9�p�X��Ɍ��D4���9�Ax^;ځp�Q(��\\���{���(9�xD���j�(�2�6����|��K���R(�FR�p�+;2��5��`�2�4�Q��ӌ�f�b-�W���{,��Th�0��(�9�1=n5�HK�&+�]�e�����JL\r#�x�\r�� ��\0Zѭ�J�#��0�:���-��%��B0���l;�I���4�`��0�����5�8ɲ\nY�H�+�\rC�j��j1���\$NF5��.5�hv��C�h�ӱ͍�Ӣ∙K��<��ް���N_a��n=3w��F��K��n#]��fP��Y�Pv�V���\"')�0*�c�ʝ')x¶9+�/��t������ P�|�ƣ@�,�H�\r�0́��p��������p��c�^�C3P A�3�P����m���S����h��0RK�pe2�X�@�Fs�X)R�v̢ x�������.\n��2������Gt���^IՐg\n�Q�ՆӲt;��b�Ő��h\"����Cƽ�.\n�P�\n��\"]������NJ!@���QI�W�r'\0���.X�'����֊�Z�^\"�%�������'D칗@IrF��H��z]W���A&��*�h�D.\\���]D,�������\rfm|�STס�U��;t` 0������f �U?��\0f	,���]\"�@s&03&wZi.�̗��N��	�x��L�\$�J�84BP�\n;�ReU�O���.iث8���4��ԢuXj͢	U�\0007�x�K���y=p�^�?d�x��a�I|��=J�b�H`���zw��k������4Q�J'U�7I�fj̘q��c���\"�M���M�Y͕\\&��)X4J�q\n<)�I�񩜨D\$x�����]r�8�\r#\"l�BL����9���QU�Ȏǆi)�.}3u6޵դ�!@�)��\\i���*O�<�aũ�>[%\0n[=*\rd���4bB�m�l�63��p \n�@\"�A\0(4d\rȢP�LҲ�#a���p E	����{��+LW.�\0�i�9\r�Y�]��Kj]-�(\"8!|]�ppq'A�7�\n�`	q�DP:�gY}��p��Zv10d�=Kf�}g{���<I����<�l2���\n�nv���b,%>ȼw�A���a6�-�'���r��YBlP}O��VQ53aB{ϒ��\nc(`0�쐂���M����/l���9�a˧��t(��o<����y�A�{�x	��r���.q<���(e�퉙L�`�娨��1wt�qݤ�U�(�i���c\r���B߈%�o%�\$<P�N���O0��13_'���F����\n\nMl��nۡ\\�dy���=�Q��a�P*�J_h!��\$�K�B�e*�Xf�b ap@�Rd��3�{qM�lb��\$Ģ;��lk_L�KM�ĭ43~�\$*����8��X��ݽ<����i2��� ���w\nC�x)����oz4싄�2.E�Ik�M�#��m��B8���o�[�x_1��[xsN`�. ;��s�g��4n0� ħ���web�r>HJ�X	�ZU���hI1��#�Cs���L\\��6�3V�@%���q섉����r\r.-4�a\r�}���	Tu�\"m.��΀H>g�OK�������*cc=�`z�sk�����8�'�\\)��j�\n�F\\*9~��}���{�?Ts�ހ���s�0[�s\\����0&Z�)4�W��{�pL�A��F�]\nƻ��Vn)�����XĄ�I��0�x��p�tJ:f��P��.˚�C�L\"�/P�E�L(���b��49�l��\r���H�p{L.���'�V%��͢MK�2�J�p6���x0@��Jn ����'Ot���ւ~�m�\$�>������pv����(�p�wO�0�\"M��	C*�p���P�8���r\rж#M3�/�'�ƵP���Z9��tB�:��'��gt!�SC6mc�8���{	)���� 0�xI\\�/�o� ��.g�� ���E��BF�q�:�и�KZM��D�����'��\r�n,�D�O#�\rL�b��p�c)\rϋ���_�lm&�8�o\"����oQ�MsH��\$�����&��pn��(cRc�SB.Ec���`�h���me��H2�g	�O�%��.ѱ�,ᱸ�&�0�f\"-��Vm^�kg!�*0���(aK�I�>�D�%�����N\$ɍ	�T/��\r1��#�!ǝd8��g���|%ą �%�8�J��(��=\$�g�:�#�%H~MK�c ~	��c1(�G+r7�I*\r8�&�I��~>%!�Y'�RPCi.�1�C��=҅���p�.2gg���	���� ���Hd���1�T�3 h��1�<�\r�2�@*���`�4&^1F�7� �Q0�SX�'�&��6B ��6��d�\r�V�r��/����1qB\\�0�\"PB	����\n���pM�����-ֹ\n7M�	d\$� 3�\n��<�B�\"��6���j�jz�\0ac\r�|���+#�.�>�\"�����c6cϦ�dj+��T�dD\r����\n�S���� -hN�gd��Xj-b��6Q\0z����CD�WE#?vog���_<fuBS�E��<q���qG�TD��>�\r1T�F���(1*�l��u�:3qT^D���pp<x�KQ�0\r��D�C�d2�΋`����4�|`��\"~��D���#��n1�Շ�-��2l	@3�b�'YB��P��#.	\$�1 �@����8���/�I�";
            break;
        case"ca":
            $f = "E9�j���e3�NC�P�\\33A�D�i��s9�LF�(��d5M�C	�@e6Ɠ���r����d�`g�I�hp��L�9��Q*�K��5L� ��S,�W-��\r��<�e4�&\"�P�b2��a��r\n1e��y��g4��&�Q:�h4�\rC�� �M���Xa����+�����\\>R��LK&��v������3��é�pt��0Y\$l�1\"P� ���d��\$�Ě`o9>U��^y�==��\n)�n�+Oo���M|���*��u���Nr9]x��{d���3j�P(��c��2&\"�:���:��\0��\r�rh�(��8����p�\r#{\$�j����#Ri�*��h����B��8B�D�J4��h��n{��K� !/28,\$�� #��@�:.�j0��`@����ʨ��4����U�P�&�J��)��t9I0�9�˰!�S��2�!@Ԛ\$��H�4��Z��&f�S�M<ը#���P�2&�:M\0�c|BD\n0�cB7��\"���X44��WAÐ��������D4���9�Ax^;ہr?V��r�3���_�H��J�|6����3.����x�B)@�\\�+�\"�I�j/E`N����:!L��%l.�5�\$7┵2�1,[.����+���y&�� @1-�����yD\r��ڽG��)C���Jl�M[�oB�nx�3,T\n;/c��P#�T��/9�C;=\\TT�����Rh8��b;\r�H�6\r�h�e;L�	]\r�3�&ejmT��R�e�2R�D�VOZ���L���V�22�\0�(��������;�SC�� ��8�3��{`��l�>�(}�Ҁw�/��h�[\n\rk^�F��*���P�<V̇r2�y��uO�YI9�����K=���0MJ���x�3C�;���qUO����W	�n(h�0�tF1�a!��\$2� aᄼ�c��j��9��b��Y�)� ���C�@?�����n5Ĕ��e�b�;�o�7*rx����_��3�AW�&B�x*����<#%�,���Q@�\0��د��&\$�M���i��p\r!��@�� ��S��xcy�7�t �cXe��Yg������[Kp;��X^W\n�\\��r�c�����`��!gn�#>L��1�fMC!8�)R�rF�� vĜ��r��:�\$�A�2s��u\\/s찜�b#�\09D��˃f�h\\��ܠdXF���:Q\n�2�����L:p��˘#2a2�Deu8�\n@P ��\$�AQ(�e*o����q0:�XMq��F��TjV��na�;�\$q8�җ�Тt��9�A�ĖCZ0���:�6�\nEQh h�TB�LBI&��2����ݢ�Q�T~i�N���B ������,��3d��O\naP�)��O��4lJ���)Ջ	��I3�f�'��1�Q���	Y	��]Q���Q�B������]��)Z�\0�'Ҧe��[J��z�r5�C�x���:M\$V	%[��xNT(@�+��A\"���n	OSO圳� _���X�ȁ�C(�I,'h85�\$��1:��띓���z�}n��GTyO\r<�X!<Vl��)�<R����U-�ov��e\$�Z�ּ׺�(wu�#޿θ&'bH�\rcuS�\\d��Q�\0\$��+�:,�`:L��%0�%�B�)��}�b�3g�b���ciQ��G �<BA�㴀n�C�G���*��\r\$�dܘ���K��Ȍ��P�_C�{f@a����X+v,z�W���M�%�y9�7d��@fй*�꾵D��s�����b����ss#Sˈ��&�qz���ܐI��4��whiB%knC	ɫcd�(³@�B�=#�!�NmH�3@^k.|�2�=��6bMQ��2\\��0�־F��Y��}\$�5; ��N�P�T����N1�B�|�l�>�H*��!�s�Wf��YD���ZFi�&���xD��F���\r��.���-�m}�L��-?!�DE��H+\n\0&p�Ƀu�R\\�\"L�(Dлh�;˸�I�ur(�7��� ����q�Q��/ULB6&�~]TJw��@0c@M\n�:)������BC�b�0��Փ�)� �b5S}����7酑�8x)I4��� V�x��3GL��M�	����ߧ���ܛ���櫾2S^x�܉����7N�.�a�\\\"�TH`F6H-�\$��;`\n��Ok����ĎNC֗T��k>�c��f�[��>H8��e��:`@��+�XC )I>+�����␬d�9�g�	Oa��%�`����wO�l��N�M��c\0o��K���(�/v��\0	�{&w����5-����v��w-7��pMB/M���Ц/�m��pN6�H�0Z\r�I�7C�\0�4�P\$xj�j-����<�p��(�T�9\00001	�\n0�&Cd���6B�.�D0& AZ4��ⲹE�9C��d2�K�`P�p(��h�,��8��)��Z^�%0����h��Hp��dz�o�a��G(dI\rϠ�����!	�xf2��!��SQ?\np.�F0�*>{�B����J��7/Daqg�C1r����f��X�1d�v;M(Є�zʹ��&j�L��Г���0W�9y1u\0Po�|vG��.b��a���%t1ƏG�%F���T��V?1��Ü�D�2�V�Ѭ� ��;���vxr	 ��P���\n�!,��\"�!1��\"�S��\r�)�\$�|N��͒X�r>�2P���&ѭ?Qj�'B?'�i~�d�#,��Ҁ����2��@�v�zQ��qD�2{'+\"�%Qo+��& �?ɼ�? �U*�nC�b\$\nf��)�Z.�ю��R�&���ئ\\�J\$��2�+p>f� ����1�\n��PN�t �Rd�\r�V��,W�Rj�\\�����& �ybjB�\n�H,�`��Z�#W�,���/�\\ِg2SxA3}���\n�y��L��&��F�q�->%�l	1��C�<��\"1m(PS��I�2f<\$�©��/rxp�^:6�!=�r;�1fx'���zVDHmӌ�l7&�L��9/6FB����B�&�EAL�T\n(�BcyB�\0>үA461T�| �5^�D���&̶��+�>f��(���4)DL��bdp��q\$D�lG��O���|d0&�\r\"j�VDl*W\0�gK�E�<UB�C��=�*�e:�x2@�\$δ!3D����{�\\8��L���t2T+\"�.G��JE1�^�@�U �	\0t	��@�\n`";
            break;
        case"cs":
            $f = "O8�'c!�~\n��fa�N2�\r�C2i6�Q��h90�'Hi��b7����i��i6ȍ���A;͆Y��@v2�\r&�y�Hs�JGQ�8%9��e:L�:e2���Zt�@\nFC1��l7AP��4T�ت�;j\nb�dWeH��a1M��̬���N���e���^/J��-{�J�p�lP���D��le2b��c��u:F���\r��bʻ�P��77��LDn�[?j1F��7�����I61T7r���{�F�E3i����Ǔ^0�b�b���p@c4{�2�&�\0���r\"��JZ�\r(挥b�䢦�k�:�CP�)�z�=\n �1�c(�*\n��99*�^����:4���2��Y����a����8 Q�F&�X�?�|\$߸�\n!\r)���<i��R�B8�7��x�4Ƃ��5���/j�P�'#dά��p���0��c+�0���<����<�J\0����	R3\$?�\0\n��4;��ގq��B�.��8R��D�'���2\r���@H�����HLȭx���f��!\0�=Ap��~��0z\r��8a�^���\\0ՕrT���x�9�ㄜ9��H��J�|;&��A(��K�1���^0��X��n=}#�C{��S���5��](7�CkH77��0�a��&޶l�:��[7#0���C*�%�0��N[����e�Y�蹼h��8�*G�P�.'��NL�B`�	0��2ˣs+e��&�B&7\r���j=0�7\rq��3�c;�_��|\rc\$D�\r#��[��:��\r6	��\"\"G���_��1���ytgQ/�=?\n\"bn˕� �l#(��1l���8�J��t�B=9!�b�;�AH��������<}�R�״�&�\$-*	#l\nň�Ǧ�w�.�sM���b ���~<�;`D�C�J3<3����%MM24�pV�N�@[\0h#DI��2� �S*��,��S9��ա�~��hn6���j�I�\0\r��@XH�����A9��v�ȑ�A�`a`렄�\0�3�`0��~��B�El-�D�Cg��3DD6�h/K��'��!�0��p ~����νC�m0M.	�d��F���#H��v8\\�1��ML\$7�I\n����ʯ�!e ����hs@�[�P�i�e_K���F�NCQSAJl7��~�Ò���![�x�v�:���@V�(��b�tlESDMa\"	HҜ�ʣ��V\\�Y�Ei�U��C��[�	o�%¸�zp'��7.���C�;j��ɰ|��y)�Ad\0�M��#JP��D��w�������\$'�T��V\\����Y��G�\0 b�9�q6v�����#�b6CP�j��\"J�*(N2�����@�IYy/s�������H!��2Uu?}3G�L�+2Rq���@\$\0AMi��\"��\0R�T�\rL����F@[����5�4J\\r@Tz�����V�tnW҃YJ�������� �\"\"\"A�-�R^g	�]���,+	@��5�!i�Kl�ÚM6������I��=n4DUȒF(UD-��Q��E消,������xB�O\naQ��&�UBg`i�&�S:׋{a)�x3��N��ƥ����;��V((�,7:II9V\r��Ǣ�G�m�\r���`�S��0zg�BD�F�,�%a4���w�m�-u_�D���cF��)E,CO.�������0pmA;vP�� q���t�W�\$일8��Sg\$Đ�@&��:6\n��3���ϒ\n46��g_����\n���'m�0��\$d�d���{yy`'��F�z3�*Qϴxx��z�A���\0t�,\ni��:�cj�G����������	�7?�OYRT\n�t�P����4����݄.\0)��sN�͗��9�c�J�jK��[��da�ɡ�D��[�x�.ݍ�D��RZ�<5\0���PZw�Kc3�����&������~1ڧ�]m�����;�`��#�\\\0\n�ؑ�}\rE؝��36w\"s�!2��9\$����^ ���֔h	�*@��AW�䚢�Z�A�q�g'\"������/+��>7`��̲���ۖs ���t'�Y�a6�A�}dc��|4�e���]_ۢ���G��ͺ&T\r���1;�˰�g��\r�:�3/�D�p���3�=4��&ed�;S���=��e���^�ջT}����q.�ϔ�����7@�?���=��m��(��NZ��}��ؤ^��V�w��@z�b�]��D�)�Ê���@;�RP�>\0P̀)���>�I�Ŋ�s�{�`���[���\r%���q?t2&�cz��;d�����py��N�2u�~Lc^ր���<���*�'�p'���ch���\0���'�}.�4�-�������4Ħ:0d��cx�*k��M���G�If�#z*\nF �H(K �wn��Č��B'`�d\0�k��H-�+)��θ��J�f�Wm�+/�gƀ`@ń�,;����,����N\n��9%n��j�	��p\"}�0\r0�� ���:#0%�J\r�ڔ��'�|�y02�d�M����f ��&V�q\n�N�O����0\"<���.�ʱPbn�N�O���1_#QH#�kQ2�.�k�����a�Q\r�'�^�L1O1��P� �%m�ُB7�����~^HԳ\"l&!Z(�B��dH�G��Y<r��^z����E���k#�Y��\0��\r̓\r�<��'˗ �NEK����\r������3���m��b�Ƃ�msQ�%CQP� G����@{Bx_�n�&,�.\0�Iy\r�i&P*0���}�qR�(I�w�vΐ7�)��{R�(�2�d�8����@F�	d��0Za�C�Dk��kdn�>��r�)�����A.��'�M�\r�m�\$s\n���a�|�C{0��9�+J�@d����h��DB�C�����2�O �4e�*'�D'�[4�3*m�u�6\$?2��S�u10�&�5J%s����9*�\\h�*�1�ۭ�U���:M�:�������&�_�H/ӼU�:�P�I�)F\n�l��c8��>>RY;��q<��>��?/2�a��2\$�Ggf=\n��\0�-��o������\"��D��f�HqA�5CcGBf��SAN�h�2���fg.\rf�9�\$24��o.��Q�Cl5b�4QsG�f�B��5Ht�uH��E`�a@�c�(gL� d��(��a�^�c�Fd�9�L�R,XR9vjz�&�\n���Z\n����zJ�!����|�t�m#C�VO��#K�O-#�Iі�&\"�/Id}�{�d	7B�? g��2 �#�2/�x�H�\$|Bd*L�\"C�@1 �3Ob:�B�,��\"@\$FT<@�aLd�R�\$�b�k\"~�\rr�sn��IP-���ӌҐ����(4Gի��O�3�je,�Y�N�U�YШ�\0��C�(PP5���'�PH&��&?Ñ�?\0a5\"Gm.(�xnk�(V��+&x\n�t�����Z3&/�(�\" �\0K�i�<�u�=\0�C�'VFy�dlg�-�N��:�4���|��Lrl`�\"��dsC5sdrE2\r�y1�I�@@";
            break;
        case"da":
            $f = "E9�Q��k5�NC�P�\\33AAD����eA�\"���o0�#cI�\\\n&�Mpci�� :IM���Js:0�#���s�B�S�\nNF��M�,��8�P�FY8�0��cA��n8����h(�r4��&�	�I7�S	�|l�I�FS%�o7l51�r������(�6�n7���13�/�)��@a:0��\n��]���t��e�����8��g:`�	���h���B\r�g�Л����)�0�3��h\n!��pQT�k7���WX�'\"h.��e9�<:�t�=�3��ȓ�.�@;)CbҜ)�X��bD��MB���*ZH��	8�:'����;M��<����9��\r�#j������EBp�:Ѡ�欑���)��+<!#\n#���C(��0�(��b���B��,��EP~��r&7�O�V:=j\0&8�\\b(!L�.74(��3# ڵ�C#���h+��#�� ˋ>�=C،�H�4\r�B0�/��9�`@S�Bz3��ˎ�t��d\$3�.��8^�����?�xD��jΌ-m��Ȧ2�x�!�N+0�cj2=@P������5���Ta��\"0;\r#(�\\�3R�Bp�ж�+�#�ܵ��2�2�!.�&��7��>*D��6����4�Z�i�*��(�0��cB;-��?jְ#\"�\0�)�(�dc����iӸ4�8�3I���/�ؑCx�?�¢��\r�ΑBC\$2@�a���`Z9�l�)�\"`Z5���v����]�(���e%7]��09�,�'�����3\\��q\0P��]�Կ�#k�9K\0P����7�l V�ű����w�M4�>ҍ�0ͮ��{�:�\"�����9(��U3d����u9�#8µ��[SC(P9�)8�3�:Z�Јb��#����_��JVeb����k����8�42I[l����6��[�Dҙ\$DRLȕID@�A�b\\Vz�CJ1��r�@]�U�о��mP n@��Ч\"2b	9)*ɵ����1B�\$��F�U:�Ua�V��b�ժ�W�]+Ĝ��nXKʑ�`R���\$��H��L>+(Ϛ�N�ÆD�,�E�6���uÖ���U�ο�4g��)jTհ1߁|3�,(g���!�x�\rK�aÛ6#tͧ��[C�'. �EЃ�t�.o�0XN	ї3`�(��]ħ'�\0����\n`�0\$��S!�V�BS\"�~ZuCK��ɀ9��)m#�ܾ=������N&�����1%D���,g�1>gA�2��ޘ�pϥ�?XT��\n-�D<����)@��]n��ʐ@f:ľD�.\\�=�0����:��P	�L*)�0g�\n�&m���@�Y\r�Y�H�L��B������ R�JD����u�h �J=5�\"8G�`.d�#6�ԃP�*\\F\\�H�(X9)�x�*S/c�<2�p̠\nhn`�/��\0U\n �@�]�\0D�0\"����S��L�A|ɤ��0L������\$)�y%>G,���ؼ�-��]�`ԄN��7�ݽ�S�ޔ��(A�� C�t���[KUk�&��)�tv��e�1�\n��O�C��\n�IS����VT䵖��4ʄ���9r�����۬Qk�j��5\"Vnkv\n�֜������r�UmYV@u���{���8�Ԗ\$��t.���W]�5F\0����M*rje�%�V�F�-�a��P�N�Q��e�WZ9�ġ���R�I�q��6���Z��{>���2�γ\0PRSQ)��;���{�s�1\"0�Aa P��|y�*��^CQ��+(��\\���\0��0cxb�K`(S�T�k\0w��\0��vφ0����K�:�h8�ht>��y�f�K��C�-�1F�{�	�����j��C&��,g�A�����Z8�Ӛ��<K���:G�/�[����\\5%��T�_¸eU��ڱz\\h6ĕ��0��_~h-Χ[�Sm���W,�BtYiy~�&��P�Eס��ܒ}��)@po%F�ppʓx�NO��o�����7↨�L�e��9���p]��l�\nXō,���e24/��\"`�C,c�R��'5�Ld�=�>R��EE�\n��d@x�M�[2��(�eđ��b�U��w:�^3n��b����m�h�Q��+��^��m�����7x��]���<}���w�@D�I�pzk�v4�wϤoOj4rT.~WYy�BH�\r:C�lW[Y�(\\Y[�\\s�g��}C�z�_�}�\r��a֖_IŦx��X�g��Z ����~�U��y�cJ\\�����w�/��r�8����CxȊ�=>&w�ݯ�f�·��P���^HެL�\rna������E�o��M����t^FN���iv�n��\"N	��G�G�~�Dn��\rTM̦40a��Cn~l��SF�L��I\"��|=��eT�%\0�p\$�P��x\\�u	�A�Zălf)��?+�/��C�g\n�0�:��Qb'��F�����)�R�f��6\rp�\no��p�)P�	��\$�(���t-�b0��D�G\n�k�4��0�\n�`�Q�0�\"��z�Q:�1?�#Q)\"��TE@���:C�LQK	ku�:�@�1 �ƴc40Ф8I���)''��@�RB��P̸��4�me(D�N	f�B�M�� ���^���V�-mOS�X�@�C��`�@��ѡJ����(�uf�B�:p�启 ��p|��;��_�N�M8�&�s�ֲ,�b�*J®��\n�я�Z�1\0.->/bf�\$T7��D&C~��h��N�4�,5�/�Dk�������(�ixl�C�9��'�0�FJ�n��8B\"� .�p�m��N*2��`+j�+?�\$���+�Bm���R�+%�+pC-R�+I�-��&#\"�27+����g�ަ�2l*�	����f�C�js\$k\\�'F�\"B��t��;�	/��)�0-�?)`�����'�U��0��-1���j�k6��L��:�J\"��#��\nBԞ��F\".\r@";
            break;
        case"de":
            $f = "S4����@s4��S��%��pQ �\n6L�Sp��o��'C)�@f2�\r�s)�0a����i��i6�M�dd�b�\$RCI���[0��cI�� ��S:�y7�a��t\$�t��C��f4����(�e���*,t\n%�M�b���e6[�@���r��d��Qfa�&7���n9�ԇCіg/���* )aRA`��m+G;�=DY��:�֎Q���K\n�c\n|j�']�C�������\\�<,�:�\r٨U;Iz�d���g#��7%�_,�a�a#�\\��\n�p�7\r�:�Cx�\$k���6#zZ@�x�:����x�;�C\"f!1J*��n���.2:����8�QZ����,�\$	��0��0�s�ΎH�̀�K�Z��C\nT��m{����S��C�'��9\r`P�2��lº����-��AI��8 ф����\$�f&G�F�C�/0����\"�눡D����uB`�3� U.9������`�2\r�\n�p�CT�v1�ij7��c�0���\r{�aC�E225���иc0z+��9�Ax^;�r5X�p\\3���_f�2H^*!��)�p�'1�@�}1m���R��:C�z:��S:��b��;���K����&.���(�Y��F=B������C�H���d����I�ū��5>,8 ��xZ\$�N�M��;G1��B���l�A��(�@�z4�X�3��(Α�۔:�f6�J*�\$�@R��b����́�ϣ�ً%����@:O8�E;b���y�2\r�����8�N1t�׎��S����OL��c�۱��D�u�Եsh�6�1�����z�=x�8��'a�QT�\"N�O�kXل;j���cx�3\r��f �SP؍���t;+^@�{�c`�TU ���v؅��H��0pA-�ߑ�@��	P a4����Q< \$�6����� �1�w����?��P`o��=���\\\r`0@4A �	�C\naH#\0��1�XE�XL�H1� �|R2�T�<>�-���_�0��E(:p��3\n��'5�uXX*�r�Z�iE �i-@ʵ��s@��'tr���3�)�p�#a����P�kq�H��Hoѡ\"Nd�� Z��iOG�F	#��u\rUk����V��\\j�s%�F�J~#�\0007.���Áoio7H|A�JN@�&��F�jVBg9��S�� '-����S���\$�D�\0�hSWͥ9�����9}�l����B���#���A5#:j a�3En��f����?c��\\�dɨ3���:PFQY\0����!����N�	[���ث3�7á�3�sN�?HN�\$#�����A(��r�����������G��)w�̦�@�S�B�3v!�ȊK\0A��2�u�Ca0a��o\"�>(���N�F�VQ����t���D��CAH!f+��[M�t^�ް�Vֈ�l����F.S��8���i\rJB��0�T�Y_�9�Q�yJ��.��2,������Ԕ������\nZGlj�F&9�U�QXei0f�5���ڡ����9+f����P(.J�Ѥ<\$D]��l'Pr�4��!��L�3J�����P`�E�'\0� A\n� �.g/^�ԗ��IBj\\�����\\\"m	��}��@M��2a8�CXTz���\0��6��q���v\0�P��1�'c�rK�S�D�AH�����N��*dgUC�<�(+pҞ9#`y��6���Z�)���s�H�rhk\0002�b7Vb��A��pI�Ld�eۆR-8\n\rd�5����,��}^}ZvX��̉���.�\0ΐ9��#ьl����l�� A�0:�0l?�(->�0�#�k�N�F0���F�\\B��%�Wh�ݦd\\�4TY�EAG�`b+��&MRF��Tx�3�i���FFgU�v��Aa l��ê�Y\\!���	Io���Y�;��5�M�����+'f�\r���f��&*���*á��\nu��b���<c�0 �ˌ�0�\\��rc;��K�F���P�5��Z�N]qg'�9\\^\r�0A[�\n���s�ް�`�[l�{�ty���)�7���|3������&���`�9�H_��\ngI�����K��F��2K�T^a&ʽ���RM^��\r\rƋ�����F�ͷ�*�Py���6&8u�Fw�����x�Hv�G��l��~E?�2ta���6���(�f9� �=��F�o�g/�+sZ}���S�&xҊG?��N�͓�`+,�Ӌ�P �I�6d�A �NBĄr�K~5�L0�\nm,p�*Z��1F�e\"�wMvעX�N��B6\0Z\n�z9o\0P	�R,@��Qo�#�>>,��pX��^�E��0Z����CO�&<�c>zo|��poh5p�v���-�0,�\np����c�h�2�c�\nM⣯�����(�x�����&@���N��fO� ���)P�l��b0��h'pZ�C��,����	�o����U��x�\n&F0 ZJ�@أ\0d:����:#�`�Ā\$%:�Z��5�&��P9�ɱ@�1F`\"�\rnO�n+/�cb{E���}�X` �тw�X�\r���rq����uL6(-��-�b&'Я�b�)p^�&%-\n/��)\r�s���	\n?ʫ\n���7b��vˌ� Mꪰ41����%4>6@��\$��(j�f�n拫�c\r���&rE��\$�Y �z���M|d\0����k�b�����կQ'�A\0\"6N`�2 �\rf��0*e����8Q��0�+�>��o���1*�*Q�P�+G+Rs)��*R���	O��R������_'2���\"��rH�Ϫ�Eg,2/��02�+���S\0:��1r�ˤ�[Rb1���m�R��R�DddI�,S8�S=%Һ\"�!`���/s(�C�\n��A�1�9&�~W��)��C\"��`A6��B:�sx&`�~F��L��<Sj��。S@�4�w.rF��a\0�s���J�\n;t���\"H�j�2����\n���ZJ\r�WCh \"g�v������S|���ϐ#�:�#`���Ϻ\$�\"�+�2m��CR�Pm��*D1�(Fk��@��F7#a<t<`+TBs�%�f��W�H#'�T+�R\nD-��FvL�&:��2���O~#�.t<'\$�R����\\G�EJGa�:\rsvGT�0T������\0��t�L3vG%c�B��)��^� ���U�ί�5�.(� )p8O�\0�����~+����?\0�WM�.�BtQ�/&�\"��n�:M��	��4��a �I�����S=�~Qk�v��b4�� ʴF|\r/-�J��' }OD%��#�I2 /b";
            break;
        case"el":
            $f = "�J����=�Z� �&r͜�g�Y�{=;	E�30��\ng%!��F��3�,�̙i��`��d�L��I�s��9e'�A��='���\nH|�x�V�e�H56�@TБ:�hΧ�g;B�=\\EPTD\r�d�.g2�MF2A�V2i�q+��Nd*S:�d�[h�ڲ�G%����..YJ�#!��j6�2�>h\n�QQ34d�%Y_���\\Rk�_��U�[\n��OW�x�:�X� +�\\�g��+�[J��y��\"���Eb�w1uXK;r���h���s3�D6%������`�Y�J�F((zlܦ&s�/�����2��/%�A�[�7���[��JX�	�đ�Kں��m늕!iBdABpT20�:�%�#���q\\�5)��*@I����\$Ф���6�>�r��ϼ�gfy�/.J��?�*��X�7��p@2�C��9)B �:#�9��\0�7���A5����8�\n8Oc��9��)A\"�\\=.��Q��Z䧾P侪�ڝ*���\0���\\N��J�(�*k[°�b��(l���1Q#\nM)ƥ��l��h�ʪ�Ft�.KM@�\$��@Jyn��Ѽ�/J��`���3N�����B���z�,/���H�<���Nsx�~_�����2�Ø�7�)6�T��`gvN+o��M��Ϫ�� �;񋦫�g6vv6�N��X���\$\$���n���^�������g��qO�i6��*�0�2\r�H�8O�BP�E#�@��pϰOӼ�=ϣ��:\rx�B�!9�ԀX���9�0z\r��8a�^��h\\0ꚴ�Nc8_����9�xD��l�>��4�6��x��|߲K�v��\"\\���z�\$����g�}�Od>/��S��R�����y��\n��\\9/�v<N��2z�9���,�B��9\rڰΏ�� @18X������of�E#>l]���j�ˑ�ZFD����[b�Coi޻�N�)�D�=���\0v)q#�@���UH�p��z��ȸ�̐�!4\n-��Ђ�H�¥�Rˡ�.L�!A6�)����i��ը�ZB4�AW���!9E�\"Gx3���\"�t���uqY�fMuƀ@	\$*���)��HbD>�j/�\$*�|0���=���Fs7\$*�B=t�^q(�5���.H����h�p�I'��c�J9%����Ÿh��d&�X�&`I�������-œ8gd�ܖX8���B�}��{�!����:\"@\$��J,����Ȳ hdᢽ��ϙ�sA�N��\$�1a%*��3�ё2�Y�R\n�б�bӉ�4h& ���A�1Y�a�-	H�:�u�I�kp	�S�����Yg�.�*5�v�\r�^y3�6��\$�j4��SKC����AE]� �¥�bD��7�j���ІF�Ld��ĺ��{�&�q��jvd�C� ����2�H�w��\\R��h)�A:`��el�M��p���a���7�@���0@�'�s�u�K�\n���s*4}��x���\\K�Z��)Zj�D\$\"�dRI�#1YQ>*fb{�[L��X��3�qN1�9\n��ܫ�\r�]�0���	)��\\�y��@�\0�Ɍ�������\"S�^�%A��%V�xҢ�I��l��c7���,G�]c�&`�{f�?Ҹ\\�۬d�b`t�I+�hO���1\0@ۃ`l~i�8'\n�C+�!���p�[d��\r��ڶm�7���\0 n�� C`s0���҂M;��2g�Z��S�\n,UHV��D&❉�د�jE>��əT(��A�.�����WI+-'p���nR�j��6lj۰r\r!�4��ψ[��PK�A���Sf!v,��gHQ1\"D�Ɯ��sŉ��H�B���9Q.,U\"ֹ�E(�E�z�Jh\$Di\n+3�F鄤�I�.�`�/�vh�B�ש��\n�J�2�'�� i�]�D�k\n���A�u���)7�G�eZ;2Pc��zP��L:�\0P	�L*L!8I�C�Iz��*4Ǭ���ش>��W��;u�և��V䁤�=a�)'X1�>%7]�&Hd:<�3eKx ��3T�A0T����(U7��y!��G���� �r�����Jл��nb�E`\n�8eԱ�\$L2�\$�1m	Ԛ3���	 K���\${β��L�!��v��g2�yz&����6=U�>�f2�H�r��C��qR;�]�R�,�M-��+n,�h��%��y�6~�^��a��z-Q�g�4�P���c�X���U]H�NaD����ݐ>\\��V���h;2��kO���Ԃ��~�h���t�Ì��|o��������v��sp�**TM+�{@���R��?�Is���\\�0G������CVV�>�ǺDH�Jl��*\r��\$�b���BF-/�FB�\"�b��j�.���4.��K��x��z�)J�08�%��'v{jT@G��k���%'�X���H^z��yi��=f�H�E��J��b�hy)>� Jh����4�� �\0�n�Β\"��b�cPB����\0�2���YP��P6i.�i�j�i֜��d�\"�jf���\"�}@�(V �u	.��ꈉDgI���do����e�Z\0 �\n��`�\0���\r%l\0ѥ\n��FR'�K.Z=�XH�K�І���@#'��b����h;B��Er8����*���6CL�!4�)�;B�'��x��GB(��5�U0p|I�\$q��1����8-�?)��QآhR��vJ��W̖X#�H�xkΛА��r Ep*���� ��<�HΎ�q��4�.�|B�#�2�B�?��R	\$�N�l��Z%&[�r��D�1�%p�B����WI���V��r�������[�\\ M��r�E��R�N=l F,�\r�~wFH�c!�(j*��,�B����Rp��,Fҽ)oc�T-�5�'l�2H�2Ř҈���|��p'P�6Ik�D�@/\0|���-X�����B͂���b�8R�+��!H����J�8x��G��3��!)6�tE��Pq#d-7�k7�Msn��R/#�(����@�	�^��U�xC�:�\n=/b+p�z�DB�;ӫ<�<E\\1�qdVA��7�\n��t�	{<(�[�ܞ���ʵ���*`U�F\0�N@�`~��3��뼞��\"�d���T�B�C4%���J��<�Ƒ�̺�� jF����l�]�P�fL�L�,�0-@SC��[\"L�r���(���I�HE�(O\n�rG7Ԁ� \\�go>)6:3�H���o���>�]9(�)HessH��>��D	�.s����GԳMN�L�ڽ�EpoL���s/0���?Ē.q�gI�k�K G S� ���QPG�P�}\"�A5Q��Q���'�N�����,�ʋ��J�wH��P�-��wu\\���+���O��WUWUY3.�V�gt����>��D��B�(J�+0��NxۥK�����k��FH�I����4�Tb!�=u�D�'!�^��%�����G�G�5��\\U�=�	^�нSX�5v�\$M\0q>�.�X5{\r�cs�;4m\\���������Z�������xNX4WXt�Zug�5g84�L3eP{\"U�N�E��)5h);f�V�tD.4�&kf��Vէ��E3�*��X��j��j��	r�գgP�Kн	�Mk��C՟l�^,��m�Եbb+���/orL���%d���e��U��-�W/L����?6SR�S4�;��LSgPw&��]kU�N�-q,�4�T�kuoET'mU|���EѭuP��p�J��<B�v�h^.hP+S����h����v�g5�u��d�ΫN,tS]�6�m,�Pl\"B��4�r��6m�{\\�V�t��nW?fNFvj?|w�5�;Ovcs�OI\rv��|QG{w�]��x�Mz��c��S�&w��7+V���ⷁ6���~ik7f��yO<�y�݃T��\$��W����UDCO\rO0�B����Ca�Gx�%K�~�e~�Q*\"e�CwwMh�\ro8|���\$\\�2|vڷS�e��cx�����M͑_���QZs��\\�8�lW�l�N��oe�_��'tU\nć6�6K�h�����v��[���qԤr<���8��+���&�&Eybņ�(��D�T�eH�x����5��u?����&�9��%��TY~S�6Ĳ�e\$>�DT�����`�\r��`� �N��C6�U9�!���Xc�<�2���%{^K��5)�:fC�x.F��\n���p)@I�2n�!s�8�3���T��:�:dv�r&��G\\D0�x�4�Ke��=��~�Z��\0)<���GF.��ה��L�qOjs[�Ԡ�T\r]5�)��L.\"��ٙXK��걙L�GC\nʷWP��n\$тK��Fv\"���\$���rh5]��&D32�\\��]+2�B�Z��:�>{��@�w4Fh58�'o����8Is��)ZZ��ř�i~�\nGA�E��Z�G��ኖs7���:ۍ�hU�~'��y�}��Ӯ��?��C�s�ke�I����\rwby��!u�c���:(�\0�wq=�h�裴�gU�F�RwV��,���c�G�4HX��ֹ=iY49)(夤Z+X�ß)E�n��d���'�B2HB�o�K1!�J}3��H4��W��+헮Ye��}��}���-jA��?��v!6TC�4���U�4(��5%�";
            break;
        case"es":
            $f = "�_�NgF�@s2�Χ#x�%��pQ8� 2��y��b6D�lp�t0�����h4����QY(6�Xk��\nx�E̒)t�e�	Nd)�\n�r��b�蹖�2�\0���d3\rF�q��n4��U@Q��i3�L&ȭV�t2�����4&�̆�1��)L�(N\"-��DˌM�Q��v�U#v�Bg����S���x��#W�Ўu��@���R <�f�q�Ӹ�pr�q�߼�n�3t\"O��B�7��(������%�vI��� ���U7�{є�9M��	���9�J�: �bM��;��\"h(-�\0�ϭ�`@:���0�\n@6/̂��.#R�)�ʊ�8�4�	��0�p�*\r(�4���C��\$�\\.9�**a�Ck쎁B0ʗÎз P��H���P�:F[*������\nPA�3:E5B3R���#0&F	@�0#�#?��<�O�ئ�4�sv�Ȯx��L�w*�O�;\0005�`�7�#s��%N�9RE�� ��j��C�|7�� ���ƌ��R[��\nD;�#��:���9�pl,C�C3��:����x�m�ѥT7>�r�3���_c�#���JP|6���3-�ˌ��x�&��`�<��QDcK�>#������ʬ°�SJ��,�7�'�*�-2��+�+B�=�� @1+�����2Q�`�6���9�K�*S#	#p��IN*.0؏R\n�8`P�2�c��˞C�2�@�:��-�=�L�i&Q�k4�e�<�9�*��T�ݨc�o�;�(<UN�6�X#�]/�����f�&�ɼ܃��������(��U᮷qZe�bx�UJ�T��cx��8�/X��7��`2�hn���}-A�3ؠ�4�LK=�)*ܔ7b(���ˎ��}�В`�!&��w*���.j��4�%)�3�%�>�\"T�#�m<*\r���78;����fj�}N�慓�!�0����Yr�X��0RM�k2A)� �u�<�8\0��CcYWe�қ��GU�R�z �՚3;�e��z\\�K5���*J~�x���j�� U|�!eDG�*Y�ug����p5�|��h�߫n8Hц�@�\n&L-���8� 4�72d`�x%K�.� I�A��-�樴V��Z�em�u��UY�\\k�7.W^S��]��<��HZHt���4�������\r4}���?A�1\"2���4�7�@F��b#1,�0��\0l%��hj��pa�	��\0�d/S�A`��b܃\nJ8R(���fy�8)�PC�*x[C�OTLF��H\n7�\\F�AJ% �c��h\$e���3��~4d�⚆\\���0�� ��Q4Q����.��\0���9�r�pϺO%4<+s�k8�K2~I'�\"0�) K�����%B�9!�*Sy�	\$L<��:��� ��,*Dc�BD06�)0�	R�2T���*̐ ]\n<)�HcRI�l��ſ��	R�d��ä\"pN��k%����\"i�1�17R�=�����\$/)�}EC���6���`�@�1#yD�[V��XÑ�?,☑����{8h�пclC\n�T ����R���!���u\r���;ۖ�ѱ�w�RAeyo������xp�3�Ę�n�\r�%��;0�Cj9���v�)�1�ܷ�I�Gn�Sȷ�,g��>������{zF�=����͛2�\$����r{ D���c�K�JR�]�B*pݖ����;�@�U�!OL�DE��Z�1�=�s'�Tz� ��. �;\$,9-��@�^�C���G�i�>ӹ���fvø\n_�a�P0�kj�����Ţ��u.�K��sؓ�1\r��Ǔ>H�:6L�@��|�[1*��%R(�j�a\"���ӽ\rrM^�d<0�O�)�I��4��~�^+%T�������	\0�ۖ�R�k\n�@�.��S�U�`e\rI=b04�ՉAe����G�e�{3e۽�b����v�{��!m��-�鳂��R&�#�~F� d���\\�CjQ�M�rLT�Az8nbn��qK͡C������)����P�Q,��\"q���})�1�cd�;ȷs��L��\r͍H{���g����9�㬢Kq�*�7%T	U(R���C~{R׺���\"��H���%�PDAV;��f�!<����q�*y�-��\$��U��˒��)��D�&�}��:�E�������cZ4�z�H���W��<畇a��.3���rG�H�	ZC�q'mgߒn��+7�^���ơ=���Oz#H����B�(���`ջ@gL��^�/�)�f1+b�%q����X��C#*�����u/�ob�/�{)\\�f8���{�J�JwL�\0��*�\r4䠘��:���.\\߮ނ�l.����P�x����o8�	���jb���E��\0Pp��tCoT�0}-BC�a\0@��̾�BC���b�8&�8�D(iz/C��ifF�/o�Bа��\$4��Z�0TbD�����6%����HPL\"�>�R�̫c O0�'X���FP���vЯHPm,-�2ѣvӈT0����0�od�pZ��M�9	i͔��؍�/(�QPiQ2��Xت5	.K�LZqqK.�-�I��g�1\"X�iNK�~sF>�zƃ��1]�\$�M|�U05��]���\\�F¹�lu�f���tv��l�r��4q���iO�#�zk/yhP�%�p��>�Rd.q� ���Q�;\r� fqb NpOf|�Q>�O�!Q�����*zrFg�FՍMO��RRԥ!�5%�a%RcC%�Z�1%'rk&�Gpg�x=�����jU)-�&P.لK\$h	�\r\0\nE�.J*�ј�ў7fi,jR�R��j�r���1h2D�%�jq�,F\\�iRin\"f�BVw\r�����#0d�\r�V������f.d#%B0���h�d��Ԛ�\n���Z�8c-Bh��K�H���5O\"�.�l��*��#�4���Ĩ\\C�o��+,<�)�Oq�t��8��V�/-bw��\"6Ԯ �&�+X�e6��གྷ��dh<-z �:J��j�8N�,��	cf�i�J�2.T��D�CJIb���<3G\r������)o�@r�(���\$���U@�۴D#�b��J�0u�\rb��fO�vpk�p�\\utHp�\$O�0�� D�d>#������N��v�\0�B(��d6`,�\$�?�vKRT7o������R'}\0��јQ�1#+������%��/c���@�	\0t	��@�\n`";
            break;
        case"et":
            $f = "K0���a�� 5�M�C)�~\n��fa�F0�M��\ry9�&!��\n2�IIن��cf�p(�a5��3#t����ΧS��%9�����p���N�S\$�X\nFC1��l7AGH��\n7��&xT��\n*LP�|� ���j��\n)�NfS����9��f\\U}:���Rɼ� 4Nғq�Uj;F��| ��:�/�II�����R��7���a�ýa�����t��p���Aߚ�'#<�{�Л��]���a��	��U7�sp��r9Zf�C�)2��ӤWR��O����c�ҽ�	����jx����2�n�v)\nZ�ގ�~2�,X��#j*D(�2<�p��,��<1E`P�:��Ԡ���88#(��!jD0�`P���#�+%��	��JAH#��x���R�\"0K� KK�7L�J���SC�<5�rt7�ɨ�F�\n/��\nL7��<�)��ܜ�E��ܓ,�K�S��@\$h��7�����BS��:�<�����.�N/��B�Ä\0��#��'N�@ߵk�����V�T	,�`@7�@�2���D4���9�Ax^;ہrH�=�r�3��_�6@^)�ڴ�(P̴����<�x�&��F�1���8*�~¨�Z��,�j�߲I �����\"����7��a���@T�9��H�5�P�&���,����l:,���.�<8;���70�m*�K��6?��\nH@P�h#��2C`떻��/�S���j��	�t2CF&�%���[2�룠(\r#H��	�x�\r#X֣.\r������M����0������+yk,ԋecn�'�lؾc���2�;~6�\"�E���=��j%+���\0��\r�Q�J�j4z\$��J�Ϥ�����.OL :���w^���Z��ʲ�jR7��26��T�8��c|�P�+^�v��\\f�^�vYI�\0�FOi\nC\n��`�\n�)6��/R�S\nAe 6�{IZ*0Ƒ�/G���R�8/�?e�GE�n'��\"v�NA�N%ς\0����rYA�<��Va�Y�Ev0D��lg�i|��h��x g�\$tU!*\n=���Bn���ԛ�Sޱ�JNa��`\\��lSZKQk-����މ�q�PܹS�N�>*��+G�t'\$�k�y4����J�z�'�\"/��£�8p�h�92s���*����Vy�lre�T(���8aƱ8�(b`3������\\i��)|5�C@i&A̚�R�S��x#(P2�\0�Q!%i�28i؜� �@\$	ԄЬv0�P�A���=ql3�c<\r�4�Ż�#⻘A�;�稇�YiY��=�<M�\n�'�����ÒC#�����a9�jd.&���I&XRFL�]g�`C1�\r�E�\\�&�l0u.yS�jxS\n������#xp\$ɨ�Jo���nT�RdW\\I�:Y�ҟ��i�bA��TG.{MLl���PЦ�	�k��`�?��z-�SU\n�Eg�r�B�(��jrp(�E<3���R�d�ɏ��\0U\n �@��-�D�0\"���̺�\$��&ʹ�Z�o�%őe�Lʣ&L�\0RF��c2#���[�y��2\$W���@ʽCa~.-K˧d���5Rς'V�ȫa;ע�\$�dG)3�d����{��ϋn���\"�N8u�ϑ��ޢ�0nI�e �U��˾P��FRr���b�,Z�<h6�|�2�\\q�50!�Ї��y�h�������1��������\$������1�p�t����Qc�7�(��f�|&jR��/�'b�Ғ�x��	�EN,����Z� rKY�hI�ҠT\nA\$#�p��ӝ������Ko�^��(S;Xb,\ndy?��\nB�E	�P)����^�\r����ШBH9W�F�BVMޏ9ۺ��1�W&���� A�x ����_-�Z��/�F�,��K�V�[q�\\^^�^�Ja��\"m��6z���-���\nhi��g����{�n�?��R�>����>�s�|C�)�l��2BH�+k�h���3��	(�9T�`ULg�~�v��:|lw7��%��2��%��%\$� Fr�>�I��5���é�7ᔒ�Y�iߛ|�qC��O����d�n8yO�[�e��1o�t0.@!�����ֻ�d�̃�����1Tbr�fN��lť�q˔Y�J�Pj��n�r_��@:����z�g��W����\n5�EK����!���?3���I�����n7����i:4�^4F|{�h(��(�%��t�����+d� ���x�#��x	{�?k�x�\"�'o�x�Iܰ�xG=�~��wrS��]��߸�O�d�������\0���!�\n�N`�J�MP��B���������ۭ܂kFϫN#�����䠴�j�,���q\0#�/�/pO�o�	��5�ZH�tZ�6=�P�Ih.�B��H^��i��e���������#��G'�Ė̬8�4�l�E+�WCBEm���2P�'P�-E��R���΀��jꋴe�g�_�\$J�,c\0S�%��Rb�.c0\\��5��Q��'�5\r�b*_F20\rXR���]㌫p[H^�QJ�ZM/u�\0n�\"�Ly�`mr� ��\r`�@��Ƽ�pt�d<\0��я.�i�7�{�@ K�0���\r/��������1�a�r�.�\n������(�)��H�l�HP��!�\"{�(=1�O	�Q�I����F&�T\"H7�U��!�qQ*���!�	J\\/�#�FO��H���\n1���!���=%ƈ��g��\"2NA\r�g��\\�/��	�'rm%q-�q2z`F����-(�P�M(��0R���q��Z�}\$ϭc�׋�'�U/Q��\"��������B̝ P	n@��2\r'�u�����/���CЈ�*	/ \\+�*P� �\\ЦiqU0�	o\rƊ�����&�31Te�V��e*6�o9@�L �`�&f@H�)k%����\$\n���Z։�J;Bj��*B8g-���\n�&���nv�����&�B\r �)�\r�NP/�`�Qq5n��\rv7��0M:\r'ꖥ�Y�3�6\$�>d��nM�#��Te�R�>ЃLkK���'(�1��c�F �e�|��+j��,p+���C���ޖDH��*�/A�e\"T8M�3#6gC�=�Bg�N˄N�B+�\"5�Xm���(�'�9I.�\$(��B�	>%/������9I�_�<ۂ��`�ݬt�m��F�~,��&O�m��O� �6���RR�\"�K�y�x5�[HO%Nt6h�Dd�2/�\n0m�\r�n�%*7�lD��\$^+hx��	\0�@�	�t\n`�";
            break;
        case"fa":
            $f = "�B����6P텛aT�F6��(J.��0Se�SěaQ\n��\$6�Ma+X�!(A������t�^.�2�[\"S��-�\\�J���)Cfh��!(i�2o	D6��\n�sRXĨ\0Sm`ۘ��k6�Ѷ�m��kv�ᶹ6�	�C!Z�Q�dJɊ�X��+<NCiW�Q�Mb\"����*�5o#�d�v\\��%�ZA���#��g+���>m�c���[��P�vr��s��\r�ZU��s��/��H�r���%�)�NƓq�GXU�+)6\r��*��<�7\rcp�;��\0�9Cx���0�C�2� �2�a:#c��8AP��	c�2+d\"�����%e�_!�y�!m��*�Tڤ%Br� ��9�j�����S&�%hiT�-%���,:ɤ%�@�5�Qb�<̳^�&	�\\�z���\" �7�2��J�&Y�� �9�d(��T7P43CP�(�:�p�4���R��HR@���\nҤl�ƨ�,����b���#�鼩5D�ƌ�Z�V3�C�U\n�^�2zK3 ���2\r�d\n���7��@0�c1I���+B�(;�#��7����Dc�K��\0ys���3��:����x���\r�eApP��!}�u�C ^(a��B�`�\r�u(7�x�9Q����6W]��3d�\$�jB��������3M�<�\$�k�ᐌ	D��U3�W��P�0�Cs�3��(��geP�j�%@�8o�����½\"%l��>��z�I�d��2Hl��b�����} \$����[~��;)2DB:��3S��\n��S0��*�B0�\"����T��z�+��+��6�s�Y��F���nп5@)�\"c�\$%�,�u.��<;1��Z�εs���X�e�Fd��짨��)A�����FVꇞ�9�\"���O].8�7���)�����2�|�ա]|���2�����&\r鑸\0��l6Ȓ�\"�M��I���Ò`_��X	���n�Lx�#ĥ��H�aBjf8AaC�4D��\"�BoR�7�\$�(HWK<(8p�AB�>m��s�b��i\r���)�^3ȂW`�C\naH#\0��Z��\$�9H���p.XfN�`CE�2v0|��3���D\$����J���jAP4\0�X�r�lp@��Hn@�1�U&Z��y�R���Qѧ��>VѺVJH�ݣ�Zm���!p�2��u;�(�O�\n� 1D�����uWf�)G%|�\";]��y/E���_��I��3\r�!G�&�X{n���GtbD%p>T��3+��RX���8>��쏤B�,�sY\")HԓC�T	·3*F!E�+� \\�6\0ą��!�2�0����a�j�p�i(l\r��R%�#0hB��\0Ƽ�\0n���6\$���T��4&'���K:�FЍ��H\n (KD�B��3H���\nRbN5��b��@CR�h1�ų�@r\r'�AzZ�\nO�h7�jH��k!V1��#Tn+!3�j��E2�R�i\$M	���2��\"A{31:�Խ4���E�dΔ��^����\$��ȅCKSY�L755�bאq�add��RIqF��N��A�h��Y�\$ѕ�U,�g&\"�Kf�q���OY����8HŒ[�f�Z9%�����ZI=x���ݶ�tDfA���7�{|�Q^'�&^�lѯ�]4��`�Yf{�\"o�U���N�-��̰�����U��M�k���M�TC`�p \n�@\"�q�A�&\\��U.*k�b��j�U*hLh���a=ۮ��]�Dʝ�#�`��J��fj0�D��<����>�;åq{Q�kp�T����\0�%�w�Xg�!�\$�>�5����Cش�Y�i����38�<������SfL[V�THh�%\n_��O�Un������H�F5��Xk�U�H>�(n�{��{���<����Vf��RJ;8�ą[��>\"�a���b���gd��YN�7�1�43^|K)��`S}�E~���]�c�&�yF}����������Q�����eS��~����rs<[��&��G�kэ�����K0����{�-V��P �0�.R\r(ai�+�)�тG�tuӹa�\\�50^[\"��\$m^����H/6�6�Ȉ��H[�r}������%UQ ��K�XM]R��5�����!�����\\�O�D�\r����yȗ�E�� �k^�;Z���H����Q!���(C���*��o�p�Dg��S��=U_����q��	�o�kӹ{�MP��׈�w�j!\"W�&�@!~��'R)���\"�i����Y��Z+&<v�z3󉣾�����6���G�����]��.D�2~���&��@�A �H�~كJ�t(x2cMKZOBl�hN��Uo��gB{��\"NG#r�'����f�����&�F�@�&�R��>ӌ��f�h𬪖�lӰp����B�~m�P�э�8/���l|�<�ǒ������*�࠰�������%p�Ň�����\$��~��K��n��\rϸ�hd�L6t纴/�.[\r�-�i��)�N%p���\"{I4daOd���50���\"#�p���	��	B%C,:��\r�S&�ƪ\$?\$a�*�p0��NQ\$�i\nN��\r�?��M43�㇈�Dl�(���.�P�eQ�mq �L`��>{P���7�\r����,P�/���0�Q���AQ�zq΀��\".n��?��db���ʏ�I��D����C�p�\"�����o�f������\n�\$�Ѫ�����{�\r\"��.M�LL��3D�<��	��\$NrT0/͘�G��l�k�T���9Rw	q��k\$�#pH��w%q�)2p��z�0R�q�(��͏'1��C&pN.�Rm\$����\n��+�w*R4��#��ҲJ���6G1��m�^�R�/L�ډ�5��/�k-O�ؑd��J����.��' �t���s�\rHOP�1�g12\0��ͥ`t��Bpkf�S87�^����\$1�θ�������\n�����md���R�����@�k�\r �\reN�Q�\0MN��x�\n���Z���﮺�,X�����~0���?��Ϋloi��v'�����;��%B�-W0-�F\"u�)PG?���4mo`�Ěn-��c�*��s�ك.V�H���%j.�\"L;��w�!�,�o002w ��CjUgU��f'����ETƅa���M��&�L�*�H4thXN�v�qj�]G��J3�H��7�	H\r%J�D���ݯ��GMVF�������o�t��чHspT��gp���6�[�32�kAG�\$G~!T��ǂN�~����UuR;QmT���2~��HÉ>K�ȵ\n��.CH��v���K���u(nfM�'�s.�܃H�7�@3�";
            break;
        case"fi":
            $f = "O6N��x��a9L#�P�\\33`����d7�Ά���i��&H��\$:GNa��l4�e�p(�u:��&蔲`t:DH�b4o�A����B��b��v?K������d3\rF�q��t<�\rL5 *Xk:��+d��nd����j0�I�ZA��a\r';e�� �K�jI�Nw}�G��\r,�k2�h����@Ʃ(vå��a��p1I��݈*mM�qza��M�C^�m��v���;��c�㞄凃�����P�F����K�u�ҩ��n7��3���5\"p&#T@���@����8>�*V9�c��2&�AH�5�Pޔ�a������X��j����i�82�Pcf&�n(�@�;����x�#�N	êd���P�ҽ0|0��@���)Ӹ�\nъ�(ޙ��\"1o�:�)c�<یS�CP�<��F�i��:�S���BR�9C��^6�X�&�\$�=q�b4��c�0��,���s��P\r��:�BBX�'���9�-p�4ӭ��.�@�29��\0@P\$8A�\n0�c��8@����2��N4\r��Apl�:4C(��CB�8a�^��H\\�֫�\\��z�ڰp�2��\r�����ϲ�)��^0�ɨ��4��� F�s��7c(��H�ܶ\rc�魸�R׶,@�:�j/<B�7'c:&��x�:�9�l�R8�,*1�|��i�5�-�è�x�� R\0�e@�7�XZ~�B5^5��(�3���Ϧ\r3�E�0��V�9Bd<��X�X���:5H\nP�p2�J%Jӧ���h��ɋ	jB\n�/�z�\roN��\n\"`Z�h��܃�	K��1:� JE(����F�tKv\r�/�SCk�3�\"��'.+\"ܼ�Ƕ����.P�!<����x��.�H\n7��52��\"MB5c\0:�����#H�J�r�b��#U{E��n��{�1�('!T��F�b�)\$|Γ�x��:V����P\r�.&)������+���俣FD�Y��\n�����9�E\",��]aMf�L�ASc\r��A�2�6�S\nA��R�ѐ.0�DC��\n4��8���UOa�U�ą�#�\r%D4)DD\nfe�+5���cN�������V��`��n��;a�\$��4AH8n��j@��\n7te�����hsRR��tV��I!&'�QE%���\\�:.E̺R�.��x/ ܼ��rm2�A��X̊ɴ���	�Gn�\0�U2��q��F�\n�E%`��u�jNal��1`0Bx��` K0�(�L��a�},���X\r����\nD'��5���9,N�>,t>n�A�	��4�PQ  \n (!�,�!8�����N(�� ������D2\n�i !���D��ZB��.dD��U�W�[�S�D����ONct(Eܘ������\$�<�����dmX�&�j��ڋU<���j�#Z�M1mU��D�`9��?`,#f���?NI\0P	�L*ՓZ!t2���c\\�]:Lv���,p����d ��3��NIGu���ֲH�)&Y(����_	E*�0`��<0���sI�_De���ZKɉ/%5@��ŎA�\nE �1�5���\0U\n �@�ԯ�D�0\"�fz�_f!#��@��Q��2�j�!������N^�6}���\n�a���U�vQ�c�r���ueX�\0�J�+�R�q����C�\na���:�T�]���Y\0(S��*\$b��P�Bd%��9���H�%&CԠ�����H���7-*b�b:-�X�5�X��Amq���6�i�\"WnQ��5��OJ��s|]�\$�}�F�Bd��	�A��w�Cs��)�w����11UŴ@ѩ]6�&��-��%f.��Av�X�S���j�J2\"����4.��='h�ܒ�Đ�\0\n?-���tq���T!\$\0�	���1]X�x�j~n�t�S��Nt�k5��x�\n��Y�A1�/v���_� �z��uPI�ߙwƞ��Hk|��M�h������X��i�ί6�՝1��8,��T��cY60��7�,i��A���\"k����(Z��'F���G��U�.�K�	�F3�2V���}�Q�/Giv�ԢsEԾs[���z�Z�\r����M��#=zT(*}I��6�@1}�S#]z	�y�I\$�>nMcl��kV۟H(3����<�x�Ⓥr��1JxL�@Ry�0nM�5�`���e8����������ΖU�+/�.F��`(2�ק��}C�m!�=9�\n�{�sH�2a�� ~*E��2�?`뽘\n�s\\Ӵx���`�P\nR�.���l��g���v�`:ڋ��oN�o�i�(\"�\0���d�-���b��\$���\"zC��LB#D��j�F`v�>`��i��n�-�pF.y�P�(��X��ի��T���c�<Pz'p\n~09��^C/m\0�r��H���7�!\n���>�\n< %��\":�Bt�nG�΁0����4\rhC\"�°3->�����BŠ�'�%(r[�=F�(�\"Ї��'~�̖c-l:��b�&�:�Os1&5���p�\r\"����vo\"�٠�����	QQP��c�Z����C	M:%1l�x�_����p�&	��j�&�&����e�%�P^��6T�8�1��qq		Q�p�Qdj\0=��u�2Z��\"�pK̂��`��30��LU�4�Ɔ\ng\0%���Y�m d�e1�	R\rr�4�В�-::�!!`�1с#\"Db) A/,�D�.�w\$R8vp�hmdI�� ��&-h~qe\"�(\r-u	�s\"�\\\$�s&P��f&y(��\$�6��<dnQ�]\$�:F�e*MorJ���\\��`&�Dh���E 	�R�v�c�\r�gQ���ROb������K1hB\$�)�ަ�FC�12�<q�t����i8'�JA������pK��cn\\�#��k�wL��.|(�vF��\n��	���z6pV4�<B�1/-�5B�7O@��X�B� �w��&�Ĉ��8c0r�5Ӝ8�C�vFS�ɃDD/cX5�FN��(L*?��1\"O<b�2�nN.&F��'؏t�g�-�����-�6ά�G�'�F�s��.�bG����7�7\\��)`�(��p�ZC}�>���Y�Pd���@�\r�~��?G��&�jSEg\nk ��T\$Ѭ��K`jT*&\"D��n���G�rL�Sc�0?��-�DÃ\\#i���|�e(�LF�n�m/�!R\n�lI� TR-X�R�>";
            break;
        case"fr":
            $f = "�E�1i��u9�fS���i7\n��\0�%���(�m8�g3I��e��I�cI��i��D��i6L��İ�22@�sY�2:JeS�\ntL�M&Ӄ��� �Ps��Le�C��f4����(�i���Ɠ<B�\n �LgSt�g�M�CL�7�j��?�7Y3���:N��xI�Na;OB��'��,f��&Bu��L�K������^�\rf�Έ����9�g!uz�c7�����'���z\\ή�����k��n��M<����3�0����3��P�퍏�*��X�7������P�0��rP2\r�T����B���p�;��#D2��NՎ�\$���;	�C(��2#K�������+���\0P�4&\\£���8)Qj��C�'\r�h�ʣ���D�2�B�4ˀP����윲ɬI�%*,��%����*hL�=���I����c˞a�\r�)��KqEÜ�K�J���s �*IK�72h�N�������k��V.�X�(l+�2# ڈ&�47Ã�<�*/���8@�����R�ЃٵG��\0x�����CCx8a�^���\\0�V#���x�7�jGC ^*��ڗ%�̗(o��|�/ʘ�60�T5V*�LQ�z�0C�q21Lc��a\0�\0�5~0���8,�H�2cc&��P�0�Cu����\$�1�C���zj:!��eO2I҄�,�{*�l�S�Zql�X�0����\n�22o�[I�.�Y0�������\0003�C=r�\n[�B�1�2Y��y�\\�B��[�S���4���ci2	������}B���)�c3O���P6f��2�&U/�b���g<��}�O���i�z1T��1��(� �C�m��26Ę0�篱�=`��T��A�q�5�UB�&���@�U#�c\"���!�yc\nO���}O=�%Є���CO����gΉ�!�ݔ���y|0��\"3��Ѳ�r%�8*X>aJ*;g�]m�W��H�&���Sk�Z�E=4���ih.H7�U?	�*�ж��Fa��7�����J]r<��qE����Z�)� �y�*)'T+\"�U\r�n ���g��E0&h2�d��I\$���XI� X0��.>I\nI]\\i�s.�����V'��1\"t���F��4*�¥�y� �M7+U���S�����B�}+M!JF�ud����t��ڻ׈w^kռ����G'e��W�ϛ�5Rt<�l�ڟOa��:�fJ#���*��J�N�J4?gl��R�e	��ɉi��@�J�a�C��9]3��g�0�g��o/�el�R}?Ѻ!FT����R7_j̖��]��e�\$��)��H\nqe8ǢAT\"������HZ1�kp��ȗbky@�ͥ8�|��ZeTL��r�W:�b�e�KFV[BXCl&I����Rd�ѓ���x����0G�L4��I\r9�5k�?G3&B��_2��\n� ��HL�^˄���\n�!��K����OMO\naP�B@@�u,:��T�Tj�1?�%H���:��5w�6��ƗH�b�<|�|� \0���-1\n���{SV^�p̸DR��0w�1�\0�)��h�/��(e��A�T�N��!)��� �K	n�P�\0�p \n�@\"�o��&\\����'�a�B!Z]+n!�H�v��	���Ђp�u���%\\��e�8�������ĘOS^JA�?G�2��/m�Q�%�\"��\0��Gz�>�V,:=��������ހJ�=��_�@�ϯ39ԉm1]\r!\0)��5\0�\rs��aY*T���	.���*H.V��\n�W�x�C��i%\0�&��q�Q[:!%_�+���-�\n��Ȑ�⑊9�\r��T���@s*N��3tt_��E&#�Yr��\r:��1��ug2\$<\0���2HnيLᐴ�IY��:8��f�62����2c����A��آ��_�8n�'������ڄ5;7���R�X�ta���+@K�!����tʛ�aP*��re)��7MY/��9SqpMy=�p�\n�v��.&0h2����C:Lv�1/�h<�UJ�5!��P� �P������X\0K�焣�9~��zv衏���o�OMѶ݊&Zvj�S�O��:N��+w\\�<s��J\rGZ�}pTRm:�-��߼�N���v1�ua��3�ߡ-%�����J���Ϫ}�\$zg'�i��v^�!�H��R� W�b�HrR�t+�P�t��V�����K�Ʉbu����������&{P��jS�*V#�!E�%��ُ��[�e���?���:e�k�IW��l�o�*v���ȝ��[�������lY~'gjO/��)��j�\0\0O�/��l�\0\"e\0kl#'\0��gȖ�mbN�K�r��h ��¨\"��h����`5��-�Ĭ.�F�hEϤ�dm�h3��I�.%m�Fƶ§�*tcp�p/�y��������'���l�ʐ%��k��yp�\n0��p*���l����\r�{��|���CЬ�\"v%�b����R���������\$<�N?�T�G��������f�\"Q0ވP���p��� �/�B*�&��u��\n���?p�����N�\r�9��/��M�&lP�)L(j�#���p��B�0�O��j��,8J4��\\\"�T�h\0��G2� يh��j��̂�/ʸ<�3b�l@�=-Ц�5,��\$�B+�B�\r�Ԥ�.�eRr���-�%��аe�{\np��-�\0�mz���P��\"&\\@ ����p�\n���C'%�^��}!�	%���q#IM���k'��\$�:�%�}���#�\n�x^ČI�j�\"�ºm+S�@�\r�@X�8��Ò@3Q%\"���-2�R��(g�am�aϐ��f�#�d��)2Q/U0+��#�/�k'�2�bR�ح\$�R��2e-#�'2b,��33\$;�),��H��ds74M�)3AS3�@0Ё,�/�dJ=.�!��'Ё0�)Ƶ',���LNO�kS��\r�9�u9�/9ӎm�G7R���8���c\0݋���/;�r-B]��m)��JD\nSB91G��9�d�3�:2r�s�>�?ss�t	�nJC\nN�h'\n ���C�ňb���h�6wj���&�E��1C�Č£j�v\"�`���*:&����:S���FI_C�#ޤ�,@�k�\r-\$5c:��|��ndP�'0���Z���vwj��3���@��Z���5,��!���N�ʴw-���,�#�ZZ�S����/�k!��u��p�iQ8@�2��d@��U@D5��I���\"QI2I\0�~c�\r�'\n�ju(~k�'�r�h���:3�(Oö�b�O4u\r��;'r씟&2�0�r�w#�WV�]MP�;	IM�VW1#��W��W�pM�\r��;e�!t|�&2<�,#�\nq\r<d.P���zE���B?�Bm��;\\�]BcM��~�#�@\r�'n�Ό���Hs�b0�\r�1�=/�MJ�㲢,`��L^�Lx+�}%	Z����Hb5vȆ���\nsR�T	\\�3�`��=�<>`A`�";
            break;
        case"gl":
            $f = "E9�j��g:����P�\\33AAD�y�@�T���l2�\r&����a9\r�1��h2�aB�Q<A'6�XkY�x��̒l�c\n�NF�I��d��1\0��B�M��	���h,�@\nFC1��l7AF#��\n7��4u�&e7B\rƃ�b7�f�S%6P\n\$��ף���]E�FS���'�M\"�c�r5z;d�jQ�0�·[���(��p�% �\n#���	ˇ)�A`�Y��'7T8N6�Bi�R��hGcK��z&�Q\n�rǓ;��T�*��u�Z�\n9M�=Ӓ�4��肎��K��9���Ț\n�X0�А�䎬\n�k�ҲCI�Y�J�欥�r��*�4����0�m��4�pꆖ��{Z���\\.�\r/ ��\r�R8?i:�\r�~!;	D�\nC*�(�\$����V��\$`0��\n��%,АD�d�D�+�OSt9�B�`ҧ3�Ԫ��\"<�+0�R����I\n�᎒]7��()I�01�A\0Ɗ�-� ��e0���@����[�Co��H���(���]��0X�(�͌��D4���9�Ax^;�tiU)Ar�3��\0_ؐp^*��ڼ��p̼�*r*�|�\nc*@1�r*�V?�X�u��j9��߉�{��\rKta�z\\�7��&7«\nA\$�Ԩ+��>� @1-(��yk8QC`�6��Tn���\0��O#\"1�y+\\X2��T`P���I*�2��+�|�w�*ǈ����@P�3�c<i%�P��Ǣ��\r��4�ʨc�@���,�1������\r�T�&�O~DQ �mt�WQ��������� �(��T��[3���N ��U�'�ϝ/N܎#��@�l۬9�=�~�w)Χ��T�X\n\rCU�bJIY1�(����ˌ0���IxT��\"\\_q�P(6�7��*���GL(�L��C9č�gt���뽟���֋��l�0�\"BW��*��U4��i\$����tH_��D�\n\n�H A+�L���t�\\\naL)h(�lG��P�k؄p���XT�Z�)fy+� �z\$&�����p1�C�b B��T-���Y(d�d�����\"�@�����2F�&\$�����BA�\n!%h���������:_Ca�;�Ȗ�#�u���e�E����Z�`;��Rv��\\!�p��\0��\"�`��>��pĨ�R�\\\"kA'�	��RJY�o\$� �ؚ� �b�Fq�%�j�r&�P�+�d��骑3*`D\$�ʃf4�!��P��� H!��M��	��=�%Q���`�lO�!�����C&����)�@ \n (P��� X��xTс'dx�#\"*��pg&�8<b@��z\$��W�fwKH�{�Q�Hq����!OD�.�T�CC��xyʀƹB��T�'�߶���C��)��Ш�IHaf��/�}��(�vfZ���So�.���b}Y%�����Ri�\n<)�F�N_Äl����D�\$\r�*GP�:�&�������@��HA�k蒲�Fi��b9�e�b��Lw�,�rN�\\80�*PӮ��3P�EP��5^Y��ƙ:����o�}��)�i�B�����M�C\n�T ����R�A<'\0� A\n��l�xR\nW���ЈB`E�wx�OG)v��9���(��Ryp4:���-a�͘ƿ#�X�T\$��LCr�	t/g�\"�%Tc�I�{&�E�uJb�^.;��p�^���j�>6��CR��\$c,���N*��7�T����ofdS4��VI�ݞ�`*l�D1<�P�U�� l�Au�<��C1�n�<,]+�k͑J�h6]�+w���AL���x��c��U�)�3�IU2���Z�a�NO�x��jm��bz%��#N�.fn����ǬU�q/m��y5��qf��r�8r�dר\\��0�r�S�ك�yS�3k�B2�`)a`�T�n[!Iv>=#p��+b\n�R���@�KѦ%	��bI���E ���:g��0�����CYQ-м5�]ڝ��N,Je`�@±ه6\rUM0\\T��A�?���8�i1�,aNt���ס�ZJ	�0�<er�%ˢ���_��.1�+gF�EQ��5�_Rh	�L��D�%k�%%l��+�I�˿�v�3G�.?Fr���q̨FE]���d|Ji�bD�g��>�]�i��A���!�h���4��#/M5ԗo��{aس��-ӳ��`ē��~�&�Dɦ?O�=V�*�/���0�LO�aΘ�{���1�����E&}A\\�]jTʴ􃞍:Z��Ԕ�B��&!�&�dzT�	Epl	���K��]�� ���!5��ƫY�G>��dg�lg\n�\r� ���|�T�g���(#�hK\"�I0\$z����ON0�8ul��&,TpChu�x�/L��`ӪhK�o't�PN��D{�n�-2��0������^�#T�nh1NQO���J�@�0�C�f��#-�\$+��Lp����P����p%�/��}@�\0��w���d�n�܀ɔ\"�&�	j3��&�\0X↛�^��R��T@��\0̅\r~I�|{P�j�\r�n�&�d<O�dFP�\r�~�:��~�\0ڵG��\r�,a��(�\$bBO�1l�#7�Gf\"�p!0`��R�p��Q����A�Wє7\r��hɈk� G��N�~�f� �q侕϶7<�q���4��F����puʘ�1��i���'^k�4�\r�7�����k�!Q�!\r�gP\0w�8��B�>��]B�Jl�P�2:vRqfb�8�?\$�N�RU\$��%�=\$����j)�7\$0a'� �S'�c'�q�1��٢� ���څ'Q�)��?1�u��(��P���)ː\n�fg��\",[#pj�f�)Q���H����ڊ��AB`��	��*K\$*�Fa �� ��\"�)�B�\0005��,\n��e#fR�NQ�!S\$�&�%L�T�1�HT�>L �`Ɔ@ơ��3ii\"��礴1ĂE�)	�w*,7G%�}k�\n���p�b\$N���<�'�9.j\"�.#\$r��B|M,|���'/'İ_Cb;�6n�=�0�M�͐����ډk\$몢�C6�F`�tJ�*s�!>�vx����t3�;�T�g!�D��|�ϐO��z94�P>��D4&�d;A�p\r�B�;B²�)��CO����tI�vO���f�'B�B��%L(Rlf�&pHuf� B�� ޣd�ЮR<1�/D�l1�����!B�/`�ڃ+ABd1�\"�bTX)yt~��g���M)%�n��X:#:\0#h(���B�\r�";
            break;
        case"he":
            $f = "�J5�\rt��U@ ��a��k���(�ff�P��������<=�R��\rt�]S�F�Rd�~�k�T-t�^q ��`�z�\0�2nI&�A�-yZV\r%��S��`(`1ƃQ��p9��'����K�&cu4���Q��� ��K*�u\r��u�I�Ќ4� MH㖩|���Bjs���=5��.��-���uF�}��D 3�~G=��`1:�F�9�k�)\\���N5�������%�(�n5���sp��r9�B�Q�t0��'3(��o2����d�p8x��Y����\"O��{J�!\ryR���i&���J ��\nҔ�'*����*���-� ӯH�v�&j�\n�A\n7t��.|��Ģ6�'�\\h�-,J�k�(;���)��4�oH���a��\r�t��Jr��<�(�9�#|�2�[W!��!��T؂B�-i�q5����Ld��.j��tCA�f�Lק��� ��h�7;�s��>�����1�3\0�3ӯs���oh�4��@�:��@�o�\0�d4C(��C@�:�t�㽔4�&�����}i[C ^)a���=����\r�<�7�x�@Hc��ω3��h<�!�\\��H2E����Iâ�F��\r%�P�0�Cu&3�A(�!1�<զ��O\"03T���i���\$�t��Q��p�Pk\\�a��w�n� ���Z{�Pz�Ok�T��i9-��q�kx 9ӂ��k��F���!�۠�\"	��Ʃ,���z�}���@��B��&lPI.�7�u�<���W>�j���il��\rb\rĵ�����U�k_\r�-h�!�H������6��}6��.���<�	�<�k	ɺ7sXC���Ǻꁦ\$Ȇ��%?N�!7������w��l�[y�p:V���5p��ޖ	�v8�����@h�9��̫^�������P��z \$L\n�PH��%H	��O	����u	q�5I)@��	�i�-]x��K@��<�L'WJ�@D\$��sNNAj�t�=�`^���C�kߑ� ��/�L�,���`�5���J˂�<�%���\\KɁ1-���	)�O��Q|A�2pj&��P��:nj1�\$B��!�le�����KP9'd���@�{T�t\\��U����}q� �X�a�v�0��f�<6�Τ�b�\r\0�V�ԛ%�\0cW��/H���.���FC\\D!lc�\0�\0()`��GF���r)Q��ެÐi��'�y0����I��7�v��s���(t� q3s�Ў2<��8F\rPēS;�R<�0� V��} d���hZ��~��Ph�T�R�q��bzK[*�'s��\"�5�DqD���\"�I4}y���'��O\naR���JC�E��`���\$D���\rΡ�W��	-�����iIߑ%!��8pZ���\$�K(���2	���Ħ�@t'q�4���A�3W��񢢘'�����-m��B�6M]�e��DV���3G��QB�نd�)�4���&�If�ufI�ۜ���_�T����GkN���^Nj��� ��{����/G󝻂s� ��z�I�	�S�eF�'BZ��IJ5=��)�1�ES'6</7v��j#ĭ��fzZ��l�)�2b6sI�<�\r�WC��ӐN�ē����y��\$���OL���n��A�\\�2\\Y�o�D��;.k�����CG@���Iԙ���r)+B��	���,jA\"��2�b��2��\\�uLIS�d� �@�BHe�G�Z��)�9F>\\�(��x ��9�4es)	�dAE��\r 	��W�U8D	|{�n��p�Hy:\r6@SHnZ:ϙ��(��!�V@d�HZ�+XuٲYR��i(d\$¡!��m\ry�p�&�G�Pْ��1@U�֡���XXZ��-Y��-��1o��4j���MU�����	�\n0w��X�׸�!��}\$����qY�!�p�@��������Q+g���˺a�XS%��U#����T�Z�ܗ�\"��ȝ�u���\$�ưBo�h�����×��xY���xUS�(�f�2�8B�Ei-Bh�Wal-K��\$���F��K�X�}s�%ҵ�\nQv�������~\$v�*��m�ְ�F��v�x�{)�����vY\0�l_>���tɁ'�K�*�����R^�i;�z�ñ���\r9��5�5x\"�g�c��W�D��z՟�AsO~�i����3¨g�ף^D	�A���@x*XGj!���l<�J���.�?\"{��?Z��������4HԖ��}���y��#�d^�tv\"	q� ��rMK���j�s����+2��_�b<5��s�6\r�\n#��^��N#m�@k婀���^�fˏ\0000M>����<0nh.\n�\$jͲ���HQmo����u�g�k�\$�Hh�d \"��cON���#�th��N������q�ls	0|�07Bc�%.�'nE�^�ߌh����\"���Ư��P�'����X�'[���0κ�6�������D�AP���0(��6��ō�:�����0g��F��k�&\n>�J'f�5�Z:lωk��#��v�6���Z�-ЄB%)�Ie��~B<%�6�6��6�T��\$�88k(����\0@V\0��qx�Ǩt�P@qdff�߬�}p�o�)�lcH	\n�\n-q�2D�l�%�x}�@`c:ձ�l\"���,Ȥ`FK�#ǎ��0��\$ǎiI�\$���\n4������j#�b����)�C2%!����.`͟���M�:�y�O/-/O�y�Bh� ���}��#����DFM�'V��7ʺ��pI'\r�Xx�i���@^&�@m��\"6��qn�.�'*� e��e�r�jMJ#b��\$*��`vRA ";
            break;
        case"hu":
            $f = "B4�����e7���P�\\33\r�5	��d8NF0Q8�m�C|��e6kiL � 0��CT�\\\n Č'�LMBl4�fj�MRr2�X)\no9��D����:OF�\\�@\nFC1��l7AL5� �\n�L��Lt�n1�eJ��7)��F�)�\n!aOL5���x��L�sT��V�\r�*DAq2Q�Ǚ�d�u'c-L� 8�'cI�'���Χ!��!4Pd&�nM�J�6�A����p�<W>do6N����\n���\"a�}�c1�=]��\n*J�Un\\t�(;�1�(6B��5��x�73��7�I���8��Z�7*�9�c����;��\"n����̘�R���XҬ�L�玊zd�\r�謫j���mc�#%\rTJ��e�^��������D�<cH�α�(�-�C�\$�M�#��*��;�\"��6�`A3�t�֩���9�²7cH�@&�b��\r��1\"�ܠ�Mc\"\r�0��I�%%4�D��aCG1	B�8: P�6�� �=�))�-\n����\rJP�1�l-7�sP�@;��COa6�@9�`@&#B�3��:����x�u��\rl�A�`��|:9��^)���5���\r��7�x�&��`�#bK����5�Lk�'*����i ��/n���/��A�d��a�CRB��0\0����r���2h:9�|�hD5�P��bC�O&��&ʌ����#����䞩�53�\"�0�:�!\0툎�(%�o���;�P�:�c�\$�i��3�<Ɗ��F��C��\0\npe�X�����)X��\r���*�� �R�0��X�������˶�ף�7G∙j�]��2C;G��MAEѮV�e���)��*%\$��]���v�ZL_���Tu{�B�d�>�8�:���6��:����ۓ���u�{��]��[� Bz�����\\��3n���cP��ho����n���>�P7��\nh�xCcfЎ�{�Pi���`�	wɡ��փ((`���1�J��f� 0��10J\r�=!f�\0K-��23���bP t��0��j�����j�BN�<�k\r���j\r@�4��\rJ�\\%0�H����a,,㷀�L�\n)F�5%�趌��9ʹXs�.�S�8��-U���}��\"%��c��\\��t.�ػ����z�蟈B�Pk�~���m�Ht�`�9#4E��:0���>rQ�m�� ��ʁ&� �K���ˑW;%h�F2P�d�98��͓\0��T�6��K���Ñ�g��3I�F�W�&��-4p\ri�2@�1�S�X�s&�8���Ƌ��2n�֊>�L��qFp����@\$\n.�(��1�2>M��!S�6�3`�ͩ�g����%x�X�w.P���S���\r�\nO	�@2%hΩ��SbX5,1�BJ�3��@t�	�I\"!�ӫ��k���8���PfAd�7�t�f� cbTڽ�T;8i�G\$X5���T&\n(�VF-���s�XU7�Q�K4[M����Ry ���=\"+�uvp�_��ܲ�iY!M�*�ڨ� �R�;6zO_l�&V\r^��%\\K��A��;�&H9�9�*�F�[�1Ĩ[3�z�SL\r����SX�X|�f���_p���J\r�6��q�tj��.禥	9�A1��%TJH�v;\$���S��Q�o�Q�����­�16�O���-��\\`p��e{8<��o�{}\n�����f�F�u�v��\n���I��1mR����y��&�,7d�y�Ej�����P�[�S -����p�̴�.X\\�\"�Sb!�O~��o��(	��@���)/є�����x��;H'מ�bk%;�+'��ze����.�l��e2�G�\r�#G�X���0n�{a ٛ�6��y=����%g���Z.�UScD^vH��Lag���ơP*�~�&�W�z�P�K?A�ǽi�U��Z:\$�4 ^W�M����Pf���5atD�������N�������N\r{Â��{xJ�oc�N R��(�T��^3������ ���\rW<G���(s�m��?��F�j�V�C���}��\r���|y�o��O8�L���b��xFGȔ^�0r�qP�xG�d���C]%8��\$��6���pTQ�Lnݭ��5��59H!��@���[���YY��y��z,����D��0GIHH�	�3+����V\rb69L���fI�rw���O��NK�)�9�߳7�ʵ&ާ�ls#E�я��\n����%�\rV0�d��J�(�7Ј��L!�A������+F	\0~łp&G����[�����`�V��)�w�'�;d|��\"ff�dƖ�k��)\0�-��p��P�ŏ��x�J:<��ȝp�pL{Ϟ%o����#\"�&Ћ�ϰBŬ�g�/�G���E#�2`�3��)\"P�	�N�N|�n���	�dw����\nnz<����]O\n0�\n�e\n���n�k��H�L���p��		\r����BPf:�;�� �ƜD�D4@���H�@��3�dau\$t��\re�&1\$r ��i���m*�BZ��lj�:[C��f�����QD���R��5�u��0�O��;�G�������\rrzbr��=�����J��|FѨ)'�+���c�S�r�ljVj��{c>�������q���LA�8;��#��tG�	b���	�<\n!J\$�*rk�\n\0�3��+oRbО�q�#m��p�bs#�e\"nn��om�x��z`�\r�-=�1%�%@���� Ƿ&2g&��b�\nq����9Ed9b^�b��\$C�#��q�)2�peR�+]ҭ*F>�1�G�<�{(Ɵ�����2�(��-��%��.��-1�\$-jW��ρ/��+�I/��������W��M1�dOr�{mLK���R�%,ڤ�-�m3��Lr[\$q��+4e312�Dc�3r�TY	�C�rr)V�hF;,~�`ʶ�n/��7�|k�78�2�ӎǳ�9b~��L�����\n�\"V�NjAx�S�i&��oX����ƿds<΂���=p��/b��k�*�\"bm`�m��_��\r ̅�_%+��K\0=P\0�\n���pN��@%���º6?�\n�f}�2'�7B�<X44��<\$D\$�@�B~nc�g%����</@M�\nEL/�N���D�.?��W�J:��81f�㺬#A�/-,� \"��P�|�&\0Ye	��=�;z@�;FSA��#��IJ��>kC�Ư��L\"0�\"�ӛ-����s�6�`���~��J�|\"�aO++/�N�\n5I� 舾f����5����o�-�cN,�Y�h	��� ��\r�,ZODxt�N��i�X`����*b\"?�.#�q&)%#p	CVd��\r��Y��i,���<u\n+�]Ś-aZn�B*�g	,2�ف���\r�p9`\$�TKC:ނ�l�W�t\r��";
            break;
        case"id":
            $f = "A7\"Ʉ�i7�BQp�� 9�����A8N�i��g:���@��e9�'1p(�e9�NRiD��0���I�*70#d�@%9����L�@t�A�P)l�`1ƃQ��p9��3||+6bU�t0�͒Ҝ��f)�Nf������S+Դ�o:�\r��@n7�#I��l2������:c����>㘺M��p*���4Sq�����7hA�]��l�7���c'������'�D�\$��H�4�U7�z��o9KH����d7����x���Ng3��Ȗ�C��\$s��**J���H�5�mܽ��b\\��Ϫ��ˠ��,�R<Ҏ����\0Ε\"I�O�A\0�A�r�BS���8�7����\"/M;�@@�HЬ���(�	/k,,��ˀ���#(��%l�(D�C���N���.\0P����\\�8\"�(�6�(� ��j�\"�n����c`��H@��lp�4�lB6��O���4C(��C@�:�t��\\(s�ܔ�@���}2��C ^)���1�@��O\n���|���Ғ��P�i�H�?8��ت����V˻����.@P�7HI2d:�B�d77��J2\$ԣ�%��d��h����@P���8\"V4�x� #K�\"TC�6#c�:��U���\0P������3�)L!�&<@̒B�M��܎��Z�����Qr��(���B�](�3�T8c�B�\$���&C��m�[s\$��j숀�/9��l�{\\��nL�ڢ�(�3�սT ��{u������69� ��m�P�id8Ķñ)�7��2�Y��^���b�����@���M3b���3��9�C\nF���!\r��aJ[�mj�)�B2��\"	 \\	cK(6�m��X�/�)iC���X��x�[��]�ϕ�QNr�)@S�UCQ��D��.�f,��La�E�N>DG�PwA�����L���Ia�ɪ��T��T��T*�X����O����ep��S#�V?�|MY�`�\$�z�a�A����J���8�Cb�P��f�����Lpak(��0���9�%t���T�QG��4���\"�\\!��:�A��`���Lz��%�&��H\n��2��I�()\0��:�eVqlޙ�\$�̹����BniT\"	_��Q�Ђ��P0�4@�ڀe�kP:���NO@�K���q='�����Hk(T���LA;_\nH��i	��,q�����,��z</�#7�#����\0�ThaȢ�rhLOK�g(��(�\\Jv\$�\0���Tyb\r��4��\nA��!�ؙ��P��{mh&=�B��RE���p:���\0�&h�Y!l1��b�QY0O	��*�\0�B�EU@�-IA80��AO��T��e\$Pǚ.�j��J��)��AG2���D��3:GP�8�\r,���)u�z���e�ً��A�.��{	so#m�ҭT����c&\0(+B�ӍqpA�D+�0��pdD�ŢH�\"��s>�YH�7v�HS3\$ȿ/ʁYl��/!�ɓV���r��3��*�x��I9�(�I죥�2�tZ�͢-],�!�ZSD5����\0�W׭(7S���ڪ~k³�Љo��oD�����Mi�|��d���]F4֞���9\";��*@��@ �mX͹�Ϣ�F�����H�D��]�B�x ���0,Z�+�\r�qߘ<k>�*<f�Կ�\"��Xuk�8���m\n-w�)�@�_�{.���(���xN��O�l�r\"x{eC�2BF���6'k&h��&a)�rLH\\��u#���V�e��Ό{�a�:�LQqNa��L��P��J3 �\\���_��쓪D6��Ի�V��G�Q�:'t)w�MN��F9|���̻Y����L��W�nv�e��pl}���.k�h�բ�n@QQ�Mrc���D<��\n�����/#��K�r�ɶ��&[�����D�;D4�˘���7����S@Pk	5����_u��[�W����'%@��RrAk݇��C��8��y��;�d@��xƬ✽�s�b��0�\\�.�.�_��X�̾<_����H���T���n'���S��w�콛�ooT뜯��p�i����\0� ��Pb��#�yip@����E<=>\n�	�o1K�����Z{��Bƃ3Sm4��T*���S�}�s���|�^��V��rfӫsۯ��>W�}]z%�Pz�i��.�i/PkB.��?{�c����>��P˃,J����&�z�pI�϶E@�\\Fv3n\r;薮��=��%��vNy��'V�����L�L�������J�}��R?̀p�#�0b��\"P��͈գ�\0��N\$��(T0�n���ҴP\$�,S��4��P:&�����N�`+�\r+��p@L�����X10Fڏ��K����F���#ZL�\$f0>␈D�K�	\$P�N��H��T��gf�E���o�3o��L���/�~a��q �Zw��.��n��>\r�Vb�b,,�BcG�4GP�(�uE(%����J���w��\n���pIk�>�N�0�Exp���tF��M�\0����a`>�D,ö@ɦ2k�(�bF(\"�ɣr\$\n�^��a����\r�@XeBq��F\\,ä(�`K� �HKkd|��l���,���Ѱ��&h�?͐�\"bs��0���� ��O\0����l��0�\n�G� ����\"t�Úg�jhN��^l�*ƫ(�@ꗪ�\$/�\0���ɢ&/�0i�-E��V;ţI���o	���iBH����8G@�Fgg�FP��d\$F� ";
            break;
        case"it":
            $f = "S4�Χ#x�%���(�a9@L&�)��o����l2�\r��p�\"u9��1qp(�a��b�㙦I!6�NsY�f7��Xj�\0��B��c���H 2�NgC,�Z0��cA��n8���S|\\o���&��N�&(܂ZM7�\r1��I�b2�M��s:�\$Ɠ9�ZY7�D�	�C#\"'j	�� ���!���4Nz��S����fʠ 1�����c0���x-T�E%�� �����\n\"�&V��3��Nw⩸�#;�pPC�����Τ&C~~Ft�h����ts;������#Cb�����l7\r*(椩j\n��4�Q�P%����\r(*\r#��#�Cv���`N:����:����M�пN�\\)�P�2��.��SZ���Ш-��\"��(�<@��I��TT�*c*rװL����0Р��#����1B*ݯ��\r	�zԒ�r7M�Ђ2\r�[���[������#�ù�4�A\0��̏�X���9�0z\r��8a�^���\\0�ʴ�z*����2��\r�C�7�Brݤ��^0��h��7���=Rm�i�h�k�\n�����/K�`�*w:�Mb�/�r�;#ܵ7��P��ApΆ�� @1*����J��\r�bH�Cp��!ǩ��6�+X�RcW�R�#���6C`�\r\nw��/�3��`�3Ԍni\rl���cp㕁B|��K�R��H����Bc3�7A_�vfP䦥#݈Oo`@)�\"`0�L+����M�ҮSS�]���p̶!ԗ�-6|{�=;��ͳ�(�6�K��9�+�\0002���q�4\"M�8ih�d�� �\"	�3Δ����\$67����s3d�%;�t��݌,j�yxe7M�@�����5��\0�)�B2���#K����&b`��L�;,\$cR�����7\n{G�c�e�Y!��V��0J|ܠb5	�T��a0D�al7l	g-*�	�|V��0��J�H�r&�đ/w��	�T�������?j�QA5N�UZ�U��;�5j��rW*�'��V���A�|��>S,Г����zlI����Bp��C�|�`�	s#��T6�T�cY��K���fL\"�0�hd|]�;xqἵ.A���L�����M�mJ��'DH�¤8dX���\$(��\$�H\n\n!ʚpPQ�I1r�x1Z�r|�JDж�8H�r&�����xw���T䷞� |\$i�E�K�\r/�((0�eV2�E��֠��B�ɑ&,C�8�jMZ�hh��@�\":��d1�#�j&i�3�t7��H��D�	�L*>@����y�=��JƧk�&*�U�l^���1��������b4QL7�E\$��cgm%\0���%�b`����	��Hb]0R�Ԝ���\n7+ѭ���P�*V�U� E	����{%X�^��N)(r��R`MUl4P��(Afᥩ���r�]����\$bZ��\n��{�T`�L�O�p�ǹ�t�y��iq� u�ds�����SrfH#�&�l���t�yr,�X�J䈫b����fQ�bn���Л�(�S6-E��1�����s.��o��zu���K�0����6L��R\"��ԗ�O�;��.aB��^��7�T�z�y�Ԁ�P���E��ړ/��U|`H&�SI^�yCe��%��b0Y�	*��\0����!��e��V&�JiA-)B�r��0���C	\0�}�?;g��>�L�#�e��<ea�	0�^;-!���#�\\����\0�,z��xKS�3)߸da�@..�-e̓��a�yY�,|�N�-���Ŀ���H��A�3�Pҩ	k�GBhdcdE��W%'w�4)+����x_�4�UP'��rC�d�\"�`�3�[8W�<=i\n^�+eemIIyP�p���A�Phmelhi�*c_�ĸkH �Ke�K�E�b�\n��\\�W*�_����s���T�/&�-��7V�H��ˢ�����{䕊ܮ#�;��Im�\\��s`)b�4�ihA&jK�R�J�t�AD1���g��\n��Vn}��[�I�w�z�OU�_��u���&\0��W0ʸO&s�R��3����\"\$����x7H����-�x[������u�p�z���+�{:��,O�㳞��b�nf��f�Ҋl��.�\\�^w;eP��I�����xl��ew}��xՍ�<WQ~�����N6�h��L�W�;���¨�M5���� ��Hs�k���zy��=\$4�3�f�`�p\r�\\��_/�<O�߾�7�:J�q��������J���p���Y������/Z���ǣ~�?���U���X�^�������/�\0MV��>��Z	� �\"8�H�\r�gZ�n��o�G���6�\0ߐ8�/ܿ>#�f�\0&4\r���\0�`x��f�X��^G���@\$�K���n��o�e��[�s������ߐR�0��ep�b��'�B%mD�NL/��9\$@��B4���	,L.��0���E�ҸDU�w0���4\n�6��3\r�&1�*�0��������.f�9h��\"-���\n��m�����	E�	tC\$�*���@����X/�m\$�M��L��RRp �J��s��\$�..B�0d��</�80fL��RB���._mV�B�1lIH����cK6��A �9@�j\n  ��L�e)�O(	@�bO\nA�*f��\n���pIr/F�,�&\n�>��҂��!!��L���4��@1bP%G\$��\$G��D~2����D���4�b�#��\\��X�4qfV���G�i�&B�C#�Ģ��.lH#�4��,b��\"\\\$2������!�\r*R��R�3��=+��+ҭ,d�n�*N\$V2�j.����vP+�?o�@o��|&��N�K���\\jj�[*�0�	t .�Dc�.�n��Q��E����쮠�-��������P6r�rK\r�yj���n�R�F1,p�\n��\n\\A&�\"0\$]�k�N	\0t	��@�\n`";
            break;
        case"ja":
            $f = "�W'�\nc���/�ɘ2-޼O���ᙘ@�S��N4UƂP�ԑ�\\}%QGq�B\r[^G0e<	�&��0S�8�r�&����#A�PKY}t ��Q�\$��I�+ܪ�Õ8��B0��<���h5\r��S�R�9P�:�aKI �T\n\n>��Ygn4\n�T:Shi�1zR��xL&���g`�ɼ� 4N�Q�� 8�'cI��g2��My��d0�5�CA�tt0����S�~���9�����s��=��O�\\�������F�q��E:S*Lҡ\0�U'�����(TB��5�ø����7�N`��#���{r֍�@9�Ä\r��;��#���14���ZW�YBr����T�Dz���Q��1�2<@����#ʲ��\$a�K�\$	V��ē)�,C�d��D�L��E�`�G9|T���������)�L��� ��T�&�VA��_4+S�6A�T�\$4B8UN3���9-��A�@�N���@9S�(��\rØ�7ձ=��(���A~�!�bA'9PW%􏩕��3M��H��#ZQK�D��y4*�^A\n�f��øs�QL��[��A�b�6��HA\n��t5��\r#����h@0����	5����4�aD����4C(��C@�:�t��T7����8^2��|B9�q(^*!��5�`�\r�\r^7�x�VU͔A��NE�\$Ўh�K4J	se��k0*�Wk��t�)�M��|zN�A \n��7=�<��������L��(�qT@��g9+A����:����0����ǐ%��E?GI,Qӯ�����R�9hQ9��vs�}�\\s�F팤�[vD\$�0���\$�o#/\$Y+��B7067u\$61�#s�(���Oq��KE�t�;�9�W��^��������G��z'�K�Z�	�'��<�I�5bz*I�n눎	լ���`J-`AX.���e)BI��X& ߁~O�Z\0��T�#��к3��C��4���\0��b�VK����゠o5�7�@Uhu`L39�@xg=�͌@��g'�FG\0�n�0Rs ���V�*�S\nA[(��cG(�x\0@��B��\ng}I#\"��G�q)Q[�CR���CUՂ�p@��J	c�X*�Ƚ�Ѩc�}�0D��������N ��*#�<A�7���I�\\�\"\ncWd�����C�\"R�)��\$p�C7g,Y��X�&c,nU�D�3(eAݖ2�܂As1fl�U*�\\��=	!�8`�����~�o#@a\rm ��@%��P��K�LO�I�V��DH�� R,ӑ�X�0f�\r�`1���d��p!�3M�7�8��b,E�Î!�\r�9�����3��Q�5F���g�d,��(����4F��t#�\"\n�&uZ\\K�, �Me�&�I��ѣj�w�9f��A�6f�ۆW����8�a�R���4p�Q�;)2]2�z�M��8�_Yj��'�\0�Ě��%Z�w	!�-��42 O����0�D A.��G��Զ�(\$�`�� ip+�\r�JqM�.x9��̂�l�����3J��B�h����T}�i(�j I��!�G%�mz�z�)e4A�r,(�G��0A>���@�����[�4h��3Rw����Xe� �'�����h�0T\n78�?�e��b�#V�KȤäA��\n]D(�u�r��t�p \n�@\"�@VH\"������dY�Dr��k2ۏy�C�\\w`I�=�E�p�ϐ�(\"[7G8�A��)jm�����s���'�8���X���Z�!�\r]\$ϸ�F���~td~�BZ�����'�S\n|r��P�B�sT�T\\OR2���5�\$  ��B��*Ȏ�T�\\ب�K,G���B\n^�ϐr�����I!�\r!��׹���S�ڿXc�|O�u��EQ�'���SZlTȃ�	��ܪ5���� �Ј��4h%x(���\0�\">���~J�YV�\n�[V9�x��'�`\\}VbepE��)1l����5����q�%Y�>+�ȇG�M��{��+�@��R�3���f4�զ��tӹ8*��A!���Ys刲*>kʛ���¤�'�no�A�.� �g�S��\rS:DhrD{ρ�D}'��V�����վ����g�\$�/ |G� �(&b�XwIv���xGk��Z�c�\$�I[?�P���ֱ9�]&�-G����P��g���C�Z�!&�oo�}��0O���?3�>Kh�֕�� [��v�|���/��␼Qb��kb��DQb6�a�c�2\$2��m���m�\$o\n�m���!\$mA����h\"@�0&��,3�m�l��k�>pF�0N�V��ZhDG����b�f�L��'R�L���\0>��A`�I@~ZE�/�H\r����#�^�С\nIx�Мw����h^���vN�I���pr\"0��f�q��y馭B�Z0��g�[�а5��p�ϮZ�\"n��� �d�������O�Q�p���0t�%�|��0��!N`����P��;omQ^�\"���B�*�T�Ί�Ϲ��� ��\0�q=�@�tǌ|�M����0l1��q�i�I�����N&#�*�Kdm�\"�6S/���Zi� �2��4Bξ��,p�p���3�(��60d�+�Ra�	/(�j��l�.Lm��m�ᬲ��]�Bm��\r1�I\$Ƶ\\��ӑ1%�&rQ�.��'e�?q/O(�L|���.�jA�C(�^��c� �n���g�:1xZ��'���(�q}-r�)'Lu�ᑮ���0��B8���.��.�2�l�����~�r�0���!�Q��W�c-g�c(s&#�+.��/s)2�g54S92�A3a3�~3\n#��W��W�6M���3(Bsf#�kγ�s\$h�3y3~M\$�8spNSW7ś9��9�6%,�nS�U3S����*�i<�4�<�?;p�0O=o�0��n��GL'���%l�i;��6S�-��9n�.-:	\$�`�,r�^�!3a0�!A�\$����Dʌ�Y/MC2�h��*\"�4���z�A �AtKD�S*O.~�-^h�\r�Vh�`�Ta6s���`޹ ̉�9��Ġړ�4���@�\n���pTIH�#�9�� ���{C�'2;°~�tM\"+޾*�,��I��J39��2m�-h8U2#&!o���f~���9 e��*�V�Bz'�P1���Aj��9pN�Ƞ{��&T��N#v��U�MNbN��8��<pJ�q3wM�d\n�H7CR5l:��\r���e��eU�kf�%�6�K��a�����~�e�p��.\\����3B��\n���\r�:\rN΄��J��B�%h6:f�M�\n��Uc�s��kosM1��t5eV�b�q5v�Y5�Z�s+h�#*L�>��\0�U�B0@";
            break;
        case"ko":
            $f = "�E��dH�ڕL@����؊Z��h�R�?	E�30�شD���c�:��!#�t+�B�u�Ӑd��<�LJ����N\$�H��iBvr�Z��2X�\\,S�\n�%�ɖ��\n�؞VA�*zc�*��D���0��cA��n8ȡ�R`�M�i��XZ:�	J���>��]��ñN������,�	�v%�qU�Y7�D�	�� 7����i6L�S���:�����h4�N���P +�[�G�bu,�ݔ#������^�hA?�IR���(�X E=i��g̫z	��[*K��XvEH*��[b;��\0�9Cx䠈�#�0�mx�7����:��8BQ\0�c�\$22K����12J�a�X/�*R�P\n� �N��H��j����I^\\#��ǭl�u���<H40	���J��:�bv���Ds�!�\"�&�ӑ�B DS*M��j��M Tn�PP�乍̐BPp�D��9Qc(��Ø�7�*	�U)q;+����v����!�<�u�B&��/����e4�\\��[�u�DD�\\T�4�TUHt�E��^u�;dH�	�Z�ev��\\��v��d# ��A�7�1D8D��@0�cyM>�\0�wB�0׎�K����QC X���9�0z\r��8a�^��\\0�Wd'	�x�7�8��axD��l\$׾�4\$6�����}OT�=SA[�aBXJ�i��\0��^1z��Yj�9[O�/9NF&%\$n\n��7>�<���9`�Ys��K�5z�^��YRL���u����S���\"b��6D��6�*�BiQ��A؜/�!��D��QP���*u�f�����j�ĵ.o	2r�Z����767ԄB1�#s�(��9T����/:���Y�e��j���v�E!�S��� _/����w�@z][O����:��ي�WF%����1�1BQ�6A'\0`�h�-T�E�W��\n�YV�G����s`)�#NjW�Q��3�ا���,�tM�0y�g��<�\0�C��_a�ǂ\0��9�l<:(zC8a>��)�\0@��pu8��9���a��b@��0��1�d@�\0\\y�	\nj&&��FJZBH��d%a�6\n����t�����tń���SA�v�V��3`�!�k@hG<BQ\nN_;db27���y��H�?�P��R���0��ib���1)0���c�}��vF�Cr̡�2�,�ҜfL�\$���nCk+��(���ۉ���5��L���┒��p�e�z���4��(� `���#^|��-�0�it�b\n�e��Dx��X)�7����ÜA�ɇ3\$�Ѫ7G&��%CL@PFH��\0PUK�#��v�1�J\0J)L���K��CS�D1��:���7�2��\r��C�ނ��Ub�?Q�2K���s�F�EG�G�H�)d�`�\"oO�(�#Ā����!�(	 uד�_U���}��:a@d\r-�x�W:NA�b��ʇ4@��m��Av�vWH\":�ui���xS\n��	�E�N��k�m?�Jdm�ʶʤ���ak���u&��b�z�Vb�2���O�5��Q�����NGd�� �\"����n��0T�O5�����b���6X9�&_�ȸbئA�\\��O	��*�\0�B�EX(@�.z�y&��n,�K%b�O�׀V���	�����_��/�\0���o�K�`�q�r[U����Ķ�4K���Z�|�/��@'+�H(n\n=ܕr)�\"��Z��%��I�	T�X%	/VfU�D�N������\$��%EQ��\nd;iE5*�J��\0t�1�k%���S�Q�݋V�F,�����TދNX)�SsSj��?x�VBg_t ��B�.�M�e�5����0\$�l-x\$,Uk�j�x���4���p|#�\0�<\n��c���]���6����%���cb)��?`(�� \n�u�1P;DZ^â�<����aLp73@ C	\0�� �҇[Q���Ϋ��/�٭O���n/��I��6*�8��.�Rj����S��DI&�|��\"�1+�4����M�g�9`�.D%�>J�������\$����&1&������ZKɉ>)GR�C�m�SVj��G��h�����\r�+փ�,N�n��*0w�q+�\$L��H�V�a\"��W�'��V\$ɱ�2�\\�5�@��i�坥����k^�ng�e\"����`�h�z<���ǳ��C��CM����-l^�n]�>t%Y��]�KYW�����N��P��IA�\"A��SJr��g��b/�|;�>�s�B7`-�:�S\\�ƛ^k��oD���NI�RNc���ʬ���Ov�d�+�����o޹��na��*����\$�.U��lf���},|�0F�����|mR-p�z�Pf}�?˒�AC��z�����r�\0002�r������Ge��Є*n63�QW	/nٌ������P�����j�O����Ѓ�J�˩Ϟ0�F���)l�\"V+�:)�p�N �z���EbU�\0,���Bȧ�!���<lH�P=l8�o꺁3�V\ntiB�,+�+1'\r����0��f�ۇ��\rjG_���U�E\r�MK��L���,�an*O��(��>��o1�����J�l�j�0�D%�W�X�\0;�\"i�{#�	��+q�l���-�J�������<'\n���H���:��k!q�%�p2'1VD��\0:P]b���!R9#��C�\$gj�p�1��rVV��\$�:�2&i��@�����M-��Q���LtKb<�j�#/AG(�tL��'�`�g)�w*21'�c!�OM����E%��-�,%҅,��,�}���)�!��.��(!D�\0ňޭ�(���G'2�*�r�rn@�C	���\nG����BvS(-N�<��B���pH���F��#��ݦ��tS�f�\n��nT��[&����OY-b�h�\r�Vh\\`�Q�_�\$r���@޲@̇�( ���ڑ\$@�ȨA��\n���pQ�\"�C����oP�@�21��2F��/@s4�bj��>�	��\r3�PAhjafNO42�0|��O��\r��f�T8c�T*d��\$AF�,����\0|�PS�.;ae�f���dv��~y��.S��@\\!�Q@QiFP�6�qTb�OP�,���Rz5�\\��ʴ��	@]m_?\r&=�^<�ʐ&F�dt�P4/�N�JF�px/��ǿ\0d���Z〬_\0���/�:\r�>����s�PZ������F��r�~���0X�%�Ãp��S�6�{!2JU��%Vt#��n��EnH`t�aB>\0";
            break;
        case"lt":
            $f = "T4��FH�%���(�e8NǓY�@�W�̦á�@f�\r��Q4�k9�M�a���Ō��!�^-	Nd)!Ba����S9�lt:��F �0��cA��n8��Ui0���#I��n�P!�D�@l2����Kg\$)L�=&:\nb+�u����l�F0j���o:�\r#(��8Yƛ���/:E����@t4M���HI��'S9���P춛h��b&Nq���|�J��PV�u��o���^<k4�9`��\$�g,�#H(�,1XI�3&�U7��sp��r9X�C	�X�2�k>�6�cF8,c�@��c��#�:���Lͮ.X@��0Xض#�r�Y�#�z���\"��*ZH*�C�����д#R�Ӎ(��)�h\"��<���\r��b	 �� �2�C+����\n�5�Hh�2��l��)`P��5��J,o��ֲ������(��H�:����Š��2�n��'���m)KP�%�_\r鬚���tv�K`(P�H�:�����4#�]Ӵ���-B�6��A(0(��!\0�1�l�R��U����l����0�j�\0yf\r0��C@�:�t���5}b9���!|g�C ^'A�ڱ��8̱�h���|�#��5��%(�ʢ�\"�!�0��X��+����=�Ï����䍸(sf���P®-B�m;�hJ2K��9�r��&{�gC���)`�!��K�������ЄH�1�Ԩ�1�\0�c�`�2�X.���\0�1��~�3���0�#*�����n9B�4��*WG��RT���� �BbU�鱋��3�4h2�#�V��`��͈`�0���&�,6m���+���P��c+�Y�t�ILe\"_8�Ø�4Pا��������`����\r2K��W�@ӃK6��(h�6���\"�Lf�z�ߩe��j>��B��	�mg��8dQ��\r�3�b��)UA���@�E�Xn.�:�Ux��3c-ἳ'U�����,�l���`��e��\$���;A%�)� �i�y-�'PLб�%%����ܞ�5N�\$nr� Jb����%@���X��5\0ܑy�>\$�}��]A����h�4�JC\"�ZfIk-�t���-�3��L��\r�XK��`{������_%p�2�FIU\"�]�=x�`�\r�z���j������\\�;�E�P�B�]��v'2❥��`���פ0>L�x�)�	YCh)��(2ԤDGdE%���;�#����B1ќi2��U��ɕ���/3�����V�}�FH�( X�Ӽ�R� k��\0؉�YmY����n(Dyt7'&�KK��(�����\"G`��CPӹBrg���g\n!zAh\$���.��o�CrmH�6��{�*h�V7��NJIY-%��ڨ��Q�,iE���bM���&��L�btk�Xy2\n�ڠD�Cs85���p���\r��[�#P\\hQ\"X��<I��/f�K�*qq1���q|��ՙU�5��t�Jͭv��P��+ m�(C�f���*ЧP�f0o\\�1[8���8c��`�I�:}\n����C�5�p��BqI:SSp<�Y�C�h�(�#�W1gu\r|)���#Vk���Q�9�~o��J�|��4C�q��}>�B%�\";vEǑ��+�Xr+�\n�8G�z�.�Q�y*)s��}f?�*�^��{\r��^DKC�.Q�M�|��N3l�熷��_�0e*p7���XS�/%�%�s����Ql��ܡ �aI�j�jMJ0�g���M���pIzAG��60[�{�L�������~M��>�b���3/X�ec�C�R�h.���A>hpi�l�p�t�{!�����^\\�}w����PK�P���R\0Jˁdn6�Z+G�\r�\r֘����x�)�H���K�g���Ǯ\\͙b�~K��)=�:���nLW���7#\0+���O�]��ᑟ��Q���C	\0��l�k�����^�k�ID��6�|�s>e��9�3��O�f0Y���Va�\$��V�N��:�H��x�.�\"���R�%�9G#ܫ�r�`/��2:|ג�4Q�g<�F6���GH�#��/'�r�:�A6]�rB�ź�T����X��9�U끯�@��s˓AG�;�bt�(�����vF;�{(trݵv����+���·<G����1@gv	5�����x>�a�8��	��k�L��1���5[�o�����{|����f�{oD=�,i�[�u���Y�4�io�����;?��\r񾦥����G@`q\r/\$�߷c�f��z�~g�~C�% �\0�6!�a�;�9�\$����DC0\"LDL�`���l���%�\"/|#�^���q�zQK�i�@2&�Rǫ�υ&�,h{e��#���e���)�0TLL�x����������J�� �d�\n&�N��ү���z�����B��Nf�n�N(�N�D�\n\r�bЦ��g7�ry]���n�'0��l6D�����#Ư���b\"��t7\r���k��^ت��Ԣ\rdք�s~#�pJ��/\"�g)�#ư\"\":@b:ANs�N����J1,�q2��(Q)�,�6Jz7�\\.*�#�(��p����L��&L�%4Tp#	��٧��0��>�P����1O�@�q�L���9#�0-ұ����\r�)�#-�g�~/t�&8�J�Q�~Q��8\\��,%F�\"�<�Nث�NwCf:�ɰ�ϖ�p����\"��*-Ѻ�ǌ�B>m��;r:f�#��kd�/�#�B>2B9��8�3!кb��l��\rƞ\ndc1�����(P��I\$c�X�K&�y\$b��.����� iѹO\r)Q4G�Ux\r�,��ѱ*���R����%(�u+�O--���������Ib� ĪF�n6���B�2��G\"dx1o\"�)0r���d\r �B�-R2,'��RJ�`bb�g�f���6p�SI\$�<#�P sD��-4�>Gsc5P����׆|���\0�j�e3��b_\ņS�#Ӕ ��/9N�f\r�Vh`�M����2��^�j�6;\"z�@��)⅀@\n���ZԎ���I9�Z#,,��#�\0��i:�I@\"8��#2K���L�#ъ�	�#2��p �+��8�4׀�,b�/e`8�vc����B��3�*�4896�����r+�g�8ń?�m\0��\n\\Q��A��֤hL�\n��llϯ�{�r��s�u�\n��x��\0�����B���h`\"�PS�L�*��ǀ�L�2d��+\r�rm6\$��J�c�BO��of9��B4�m\0��+4D�:|�(sJ��&Ԧ%�1K�v��F����\r�	��V�=��-T�Dv���zJ`�w\0�J�BTT�@C����CU�G�ôj�ג|\"�� k`I��\n2)����?��";
            break;
        case"ms":
            $f = "A7\"���t4��BQp�� 9���S	�@n0�Mb4d� 3�d&�p(�=G#�i��s4�N����n3����0r5����h	Nd))W�F��SQ��%���h5\r��Q��s7�Pca�T4� f�\$RH\n*���(1��A7[�0!��i9�`J��Xe6��鱤@k2�!�)��Bɝ/���Bk4���C%�A�4�Js.g��@��	�œ��oF�6�sB�������e9NyCJ|y�`J#h(�G�uH�>�T�k7������r��\"����:7�Nqs|[�8z,��c�����*��<�⌤h���7���)�Z���\"��íBR|� ���3��P�7��z�0��Z��%����p����\n����,X�0�P��>�c�x@�I2[�'I�(��ɂ�ĤҀ䌸�; \n*��0\"sz��4P�B[�(�b(�G�\n�ݠC��&\r�˒�T��l��# �Ժ���?ì(c��&	�>o��;�#��7���؃@�@X��9�0z\r��8a�^��\\�Q�s�=�8^��%Z9�xD��k���#3ޖ�Hx�!�J(\r+lf�̃\n\n�(H;�5�C��᠗T`��j8@�.�P�禌0�\n�T�\"!(�.x�a�z\"%��5X����r�4�5H�\\���0��u�sB3��L�2EZ\$3�!� Rw�j[8\nn�&3�p��\"B�8����(Nz_F%�p��<-�ۣ)�QFK�B�)�\"`ߨ R`�0+���ǹC�?_0�0��ȣ������z������bγ��\0�C�\"��g!G����t�M�C��4��d?F (��'#x�3-�2KC2�2)y\n���	�N�76�C��Δj�#sBr��uza�K�N3��+{�x��R�����7b�)���#@\\6p^�7OÓ\n�ǎl��(ާg�3�`aN�t&�t�PI�H3	|�DNyb�X�AW�6�[��4&�X�f�U�JKT�C�KV��6G������y̕�w��Z�m\$��?2w���-|ə}'c\$\\���\\+�x���X�@�,���ZW'�j,-0��0�\0ƽ�I��dm��pʞ��6O|��v}��%I؝�C1�{����ƷMڤ\r�|�ǥ���!��#����f�|6(R\"�|�\npƭ!3�.!�.�\r�P6��X�`@@P&���SKAfSϞPB��μ�B���E0�yp�y���I�?�MR<MY��I+�Or�H��R�JN��6&4	��F	�CB�H��C\0(%1��f񎍯��ǥ����TA(�D'��R�y�3�^j��s\n<)�H��C�?H����ƹNb} g�	�v�a�O��4��\0��e�\r苑�6GW+�l��O�\"��M!2�*K�hm�IN�Y6�ro̝'(jt3����J4�A<'\0� A\n���P�B`E�h�5�v*L�BM��%��q�NG�ᕺE�%����J�#��\n_Q2?\"c�iz3�!'z�v��{T�bN�h\\C��� �6�)Hs-�ͷG)m���It��`�{��1��꿒d�h��V:���c�ٖB(VYKysT+�Po�2��BnqJK���7r�[@PC�T4�����a&%	�4���t,	HH�7�M@_AG\\k�TEO�a�|ڄ'��{�k_	\$7��R\rL&jm��Q��E�bU�,A�M]���M܌F��-:K�T�%��)��1���*���5,���#<��E�M����t�\0F*�\0L�N	*;�+�����a`�f#\n�H�N�J2vQ�y7%��W��\$�j��V�:V�M�4y��!�U�6^�d�9f���46s\r��B� Ҙ^w\"\$L���4J�)��cO�>N��42����I^�M5��=x��{9���		*���K��t����rq�O=�{9r�#u9����!j��; �6ͽ�M!v�#F�9��0��L�(�<��~���q�և[_�7���d]��6��7�T����ׯ��a9����[�ݮ�A̅��oi���iT��/X�2���e�2���	���~\"3����b���,�My:���ۤ��N���N���S�\rA~D`��;QmH@�q#���G�g?+8fɿ��6��B�M�_���9�Ծ9{򭴏;Vu�9����VuZ�����6�^&�x���Y�{�'���ú�MV~H����2&Lʯ�]��2*��X�����pǛ�,�:��'gD��)��|Gz��@�yU�`B�~F�}f-�}���sZ����A�]7qx�on>aڀ��|��~������G\r���<�I�~s������EK�C�HB���N��~&>��(L�FG��Cv�Ϡ�ؓ���P������F-�. ���Z^O���f������2\0�9Ǝ��ư�/�菂:&l���`�O�%��Ӱ~f�P�em���&�2�F3\"-	���,d?p�O��0Y\n���Ypq�2�0�\nKL\\	�lL���NvɈ\n�������pM���O�i@搀�,�H\n;��&!���\r~!\0��b��v��Nâ}�.'���J����-�K��C��`�-��w�^5FR�1� D2�#8�E@��΍��F��\n���p\$����\\��ь�L�2:��8I�K��%Wj�\r��!b.�.6+�(�軧H���'b�iO�_\\\r�i��:Ll?Gm%�����&~n-����Du.���\n�\"v2�/r�O�\r��3bf��\nF�ju��g\n�Qrz�q����\$��>F��'.`¬����V��̊@����\0�D�-#�'�de\nh\n�1#B�'���!���r�)�e#�k�(�]����0���\0�>�~q��";
            break;
        case"nl":
            $f = "W2�N�������)�~\n��fa�O7M�s)��j5�FS���n2�X!��o0���p(�a<M�Sl��e�2�t�I&���#y��+Nb)̅5!Q��q�;�9��`1ƃQ��p9 &pQ��i3�M�`(��ɤf˔�Y;�M`����@�߰���\n,�ঃ	�Xn7�s�����4'S���,:*R�	��5'�t)<_u�������FĜ������'5����>2��v�t+CN��6D�Ͼ��G#��U7�~	ʘr��({S	�X2'�@��m`� c��9��Ț�Oc�.N��c��(�j��*����%\n2J�c�2D�b��O[چJPʙ���a�hl8:#�H�\$�#\"���:���:�0�1p@�,	�,' NK���j���P��6��J.�|Җ*�c�8��\0ұF\"b>��o�������2��P�����%n��B���4�l3O�\0\$��x����Ԋ9�r91\r�  ��j�PA��4RCI��åL���سH�pd臎����EJ��t�㽴&5r��.�8^���E����#R��3.�j�;���^0�Ѓ �\rʛ��i\\\\�1�*:=��:�@P����Os<ͪ;�\rأ�'+î\"4�t����ȰJ��C�V��U#�p���H�(�0�CrL�Uc�UY���SL�(�0��b;#`�2�q#�v�1�K\"-'�Z��i�4��\"̗��C2��Td5��\n3�u^�#�#h�%���ފb��65��%J.�K\"7��-0�P�5CRt#��C��Ȋ|��^Z���X;�yB��I�����X\"\"��e�b�f1в-w �L�)ӌ PשiXk2���`�3l�zj*��A��	8�_���]Y#6��#k������3ʊ*��%6�|2��R�T��ȼ��1>9`�r�Xa!�9�<�a]�y�R6vҹ\$7����8|J� ��3�X��i�i�4�`@�H2�ZET�\0u���^�����J�Q*g�@@_�y�p�I�K��&���dqP�J��C�і��Z�amu��Uz�K�r'T��J�	.��3����kLK���xTPI\r^E�U	#�'��(rg�	#�*up��1�oD�����Ha�	Z��v��Ҟ}�4�,�y,h��3���Q�8Q�~��\$p��#��(��S��\$n��'�AU~�)�������?4a�Қtvh�AS6jѦ��H��so������I�7'\$잪T�p���\$h��H	�AM��T���(L�҅�̧RgM�SE�ŧ��S	�'�2��2^�M��ADp��\0� -w�\\ٕ�Sg�1E����vK��t�6C�@H��NN��z�b�����QMa/&*�1?����j4�\\#I�Vʋ�V�Qt�C���&��R�QHb�P��/2�E�p \n�@\"�k� �&Z���/\r�9B��,X�A0��D�na�G\n�e\r�O�\0O\r�\0�Z(6Ï�l�2t���J���?+D�G0q�룶-􎺷n��!,T�����4\n�R[�78I�k&�d5�TH�c�Ę�\"7�5VI�;�W��[PuX�9�޺ٓ�L*�\r\$*j&���5�`(���{VE�ʩ���O^]�m��2�v�/y�Se�<8L��ET�L!Ø	�},M��?=p����C]�������{�Z�P�D���<|@S��=��u�P�U�\r��20ĎW�t\"΍E#ua���~\n�!��AEj\"W	YZ P���SX�;/���H�)��8�B>�	I�2!G7=��?8�E��X2���9����A�R��\$~}Q�+�pv%��]W3�8Y��̈e�n`���u�����6�����j �<��%U��9�OJ����E��+E5D�KVײ��-�[D��H����&�,����C@Q\0[x�\"��JJ�����!�+��I\"#��b���|X��~f��3�3*[���RQS\$i{2����S|9R�ő?���N&�}�:y��<}ܱ�,x� b�U�h�kʃe����Cns��7���JC�s�d�ոL�u!͙\"���\$�N����{�6�QAp�*f�!��P��X)A8�[t�m���]�`�s�O�m���W#��1�.��ĖC9��CU������ܴsjA]��VL�۳X�M%��V���+ȣ�'��'�~b`�7Z�moǑv�;��v)�x���Q�1V,=׌a��Rz�#�����@�L(�:����<H�I��tU�!L�a�̩\\%B�?�W�.ϯ� xԎ�����\$�����͈L��n�ؚ�ヴ2&��j��\0�\0�Pb.X���bv�#��zXI#\$�&�(gP,�X����0:�.��C�6İ'L����SnE���&Vn�C#T\r��;쒸�<J�0�O:ɤ��\r�'�Njf�n�%P~�p��&3\nF�j�3,s�\n�\\uD�:��H������R�03�\r/\n���&et�B>�:P���sc���%�\\\n�j����Q���Q%��z�l`Ʊ\r@�l��PA�i/w	�8�,lą�<�1b3C'D�`�ʣ�V���\"��P���q/2C8bU@�X�h1�&i�f!f�e�G\n��5T\rЁ��G��\r%t�Z}�Tgb�\nq�m,�d	\0��֑�\r��	rf\r�V\rb<\$'C���.�y�z&Bԣ�1C.�M@�n)8}�\n���pI�VeL&�\n�c�%\"8���v\"�Mh�B�#oK#��\r�0#B�#�B�'t*����	��`��T(N�N1Qt�L~\$ E'�D�  � q�8�a���j	���D��k&ٰ|���x�x�F�d��C(\$��gq^�oJ.B��-rL(��%�Y �6�8G4���q��\r����.����*c8�BF8Q�'L.;-o�b,k��}�4���L�FnK*n��DT��4 �2�R҃��`�ae\n�t�#��\0�:��PRB��t)��a����V/�(T\r.@NnW�*�h���N��`p�\r\",s��i+.��X��TJ �	\0t	��@�\n`";
            break;
        case"no":
            $f = "E9�Q��k5�NC�P�\\33AAD����eA�\"a��t����l��\\�u6��x��A%���k����l9�!B)̅)#I̦��Zi�¨q�,�@\nFC1��l7AGCy�o9L�q��\n\$�������?6B�%#)��\n̳h�Z�r��&K�(�6�nW��mj4`�q���e>�䶁\rKM7'�*\\^�w6^MҒa��>mv�>��t��4�	����j���	�L��w;i��y�`N-1�B9{�Sq��o;�!G+D��a:]�у!�ˢ��gY��8#Ø��H�֍�R>O���6Lb�ͨ����)�2,��\"���8�������	ɀ��=� @�CH�צּL�	��;!N��2����*���h\n�%#\n,�&��@7 �|��*	�)�*���R��<HR�;\r�P�\0��s��(-˖ޭ�h��2(���\r�Z�# ڶ(o��?(+�8?Ј�1��2��S������:\rx��!\09�P X�(П��D4&À��x�]��]��Ar�3��X_S�#���J(|6���3-�g-�x�@��z2N`P�� ��:���Եc��2��U��#�`���ˈŁB��9\r�`�9��\$<�\0HK�XC�>\n�Pˈ\r�|��\rF7��Z}��p�3#����p���`�ȪZ5KL\0�0�*^P:`+����@�3�k2�d����W�KS��y\$��r>��`\$2C\$�f��^�����0\"���k,���M0���H�wy]��4�\n5�C+\"	�,�p�0�9^Ϙ�����w/+[\0\$���~��o=��.�}�� U��΢ ���}ǂH�F2�����	���5\"��6ƪ��;{Q�x�O��*��t�͕#��U�w�l0��*��W��aJR*���ؿR��)��ߩh@�5.�L��#!�0C��yL\$bHA�%!P45sD�uR��B����Z �0���@��:�(��R�L˒�[d�a���s�l'a��?�j�KP	����@t%\$��� Z�!M�0&ĥX����V��[+�t��X+\rb�劕�ZY��49��8�\$!P�!^Ya�I�`ҚxN�Y�?������a�tm!Н�b0NvGF�ɞ���0��feI-r���1wT�*\"�C2^Q�UT=p��K\\�S᠙I����c8�4��4a˙LTk�'R�0P��\$�\\H��@P�\r�k�AE1I�(\n\\��ih��8`oQ��\r.`2�90��Q��@�r���k�K�֩g���^LL��l����\$Szf���C�P[DC\$�\$�����Q�]�*�O��/Aa��;��tj��UȀ�H4�|�:�p\\��i�:	�@�	��i���V^\n���kI2�[�Z\$��t�(D_��7�pH�@�d��&䈒Jx@B�n��W��J��PFi�δ�])b��)����G)�sD2�(ι��ЂxNT(@�+��A\"���f�O�=7/�p��Zm	�.��\"���[ʛ�0��h\"J3\$v�ܑtn�+\0����)�X��tCT��\$a��\$�����]+���)7�I�\n��^��t|WCJTJ����]�КSRb��O\$����<�����֎~`LP�U�C��nu�Z�d�B!�o��?��bY�!R������]��*`�R\\La-t\r����D�;^	����3�;gA'\$��ؑqKxT-/(���l�jD��9'�G\n[X/�핟i@Y(RTq�y;T��y�A���Yp�BH���8�y�Q�=�@ː�>�����\0/*� �F��i�	s�*0� pO	j�F�Li�pr!1(�6ͥ�� �ZsH��&u��� �72�O�8z��<�Y�8t���L��]�������ꭌD�T�7!�C����!IP\$������fݜ�N��k!�1� �)3/���\n�y\n����O��,����K��{�A�ކv��KB�b�옹8\0��9 3ы�-\\z�\r�GՓ��1n���E�ť\"�-�\"y�x��M�5#�>-^�С����yg4 5���Nd^-H\nZشo�¿�\"f/��\r!����P�e\r #~�LßS:�_�B.���R�:���!�j�d?d%����FC\\1���IrT�ׄ��\\��t�'x>e2�SLa\"G�����A�d?��]���c\0�?���W���v�ߛ��;�bS��~b�;fMX��Ơ\r:�	\0�T�8����D��g���D�\$x�_t�3��\r���KY7O�ȿ0���y�}>9w��cʳh��4�T���_v�`eUr�y\$��L-��O���ۏ�\rʭS�0i#�d�'�DwO�)������&z�o�-�v��^���e\0����T^<5+i��2�D�p:��`6�lQ�r������H̩��l��hO��S�����ID_�\r�Fd��ݤ`�\\\r��'�.A��e�������gl���p�(pP�|\$�l�\$���\r��~b�t�<��M��0��\r��B�]~b�)�j�pF�\rqgM���>��9H�1?Q(K(bе�*Ei�j�Be�9��b��8�e�V*N?�}�A0�lh�,�:�N3�0\0�Fƾ�ʪD���zMIні��l\0�3,�.ꄝI�sC��B�A�tSf���R���0���Ȼ�*(d%z	f\$`�d�\"�d����d�:�!J�1�ըH��\0���D`�`�A\"�� �M4��CX�ʹ.�ā�&ZK&\n��r���\r�<%1����tnF�y%\n����o�ܨ.gP6P}�~�ŀC��Q�\0#��N�#�\"lh��>߮°��Ad^�E�6��0)����b����ˋ�0l�c\\�S�H��s�q,B1,��\$�q�~6+���0���-j�5�3�s/˛-�D&c\"�.G,,��\\`���J|#2n0\0��.��2�0�m3-�]��\\K���fq3- c�ڷn\ng\n!t@�P��?�x0�2P����b��.&J	bD�Q1�F���\n�-iF�n\"��";
            break;
        case"pl":
            $f = "C=D�)��eb��)��e7�BQp�� 9���s�����\r&����yb������ob�\$Gs(�M0��g�i��n0�!�Sa�`�b!�29)�V%9���	�Y 4���I��0��cA��n8��X1�b2���i�<\n!Gj�C\r��6\"�'C��D7�8k��@r2юFF��6�Վ���Z�B��.�j4� �U��i�'\n���v7v;=��SF7&�A�<�؉����r���Z��p��k'��z\n*�κ\0Q+�5Ə&(y���7�����r7���C\r��0�c+D7��`�:#�����\09���ȩ�{�<e��m(�2��Z��Nx��! t*\n����-򴇫�P�ȠϢ�*#��j3<�� P�:��;�=C�;���#�\0/J�9I����B8�7�#��0���6@J�@���\0�4E���9N.8���Ø�7�)����@P���mc���B�N�Oc ����\$@	H޼2�D�9#Cv6\r�;�=9nhº�k�Y\0�cUJ ���?:4p+�<C�9A�1����3�\n�@:\rx�^�p|\"Ʌ�\0x�����C@�:�t���5�������|9��^*�^�7�p������7�x�*cx�0�4ܳ1�[����`-.�J�hf\$�T���b�%J'>� ����,J�:2��3:9�l58Y�j�猨�	cx�\$(�{�L���B\r���#p��I.]^(��F6��\"�xZbë�ӭ�\n9W�%=b,X3����\r��)��(��q\n1����:0�H��0��0�R\0�|��'�:��%0�B���������5�k�@B��&P8�������X��M��Ow� �����:v�܌y\r迹��S�80�h��Q㤅Ih@P�6�|`��}>_����eKC���È^��������:`(6&#�AH9	Ma�3\$>M��yT\n��#D�IY-�N�C*�>�x0�V:���An���2�Ε��9`��`�\n�e�o�4A�g!�.И�B�Y\nԉ SP�+,��\r!�8�Pq��耨a����¤�PKf��:`�C���X;�\"�Ȁ aL)`\\�E���6�2RP��A�:��^\\�C݂J,:�V��	'�<����I#`�ρ\rD\$��'��<7����L�oB�ek-���\n�\"ƌ��2�X96QJ����=��JpMQ��/\"Ila��̔�������'DJ���1�k��V��[�q�Uκe	�]��6/�D�|^����КɄ\rR�(��p���0��.T�}G��Lb�A	aA�������� ɬ1LD�)�(@�(��M���X���'C`l\$��)�Y�a�:%~�C4��*�+R��j�PsT�	��V�]�`s3�R\"W��P\n��t���UQ2(d��(!�^LI�vp����S@���@J�V'@�=@Ú4�\r�'b��Q\\�-4d-4\$0���0��,��	F�i���>QX�cHi�d��0�a\"��%(�A	�;p���\$��9�/��-�.sӉ6	D ^��P���(Ց�ğr����T�O�i5�95� �j�,>e̓�q^c�+w\0007�%\\&Js�{U��	M1��:6�d7[@@p0iTDv����H���TЪ��d(!0��\0;��\0R��J2�?��0T��y��9��b�\r��%�z�Gj?3X�;�P[�\n���I�uS�k#�Ӑcj��2�2wo�Fi�'Jg>��[q�	ǵ�6Ãl0�\$��v\r�Pt��Їy��.j牒f�(,�I�΂#D���կP�\n�)��^K��/����SE`��D��L�x��ƔZY�)��C oX����[��|0�d+����uSRCQP\$��j� d�7����|ke\r��fe�̞����C\\�0ID(�k`&�Ҕ��LW����ٟm��Q��<w��I�?������\\�c������H�8�\$V���x��+ptn�(����K�2�lX��1N����{ka�����>)x��F�m�\"�HR.2U6�\0�V�4��9#SP[[r�&*�&C��x�eH�\$��\\_P !P*�ƶ%�ݎ��=EBo�T��:�>H�5�y�2�\$όy�BA�vc'�M�^\$��u{\$[-����kې�5�=#����W��a�a����?k�/j2q?�#���2|��&Iw����,��K;�|��B�%�0����W��л�;i��|-�j񳮻�&�	�+�z�=����/��gPG��XZ����5�_X���.�#oZ��<ؑ�(��/MXg�����L�Cv��	=��d\0ĭ�2���aI�5H\$(�M\0@�P�L�\"JF�K�0c�� ��;�Te��aC�#�R����>�pBJf\$�|��*&�:BD�ې@@h8�\r�(0mB��0H�	�F�w.K���Pt߰���0y��ۂ��̞um�6L��F�ƶ�!6Ʉ�\"�<&ԠD8*\$\n^8CN��\rEx9&�ʬ�A��4�`\n��(�|����w	�L�缳�PG���h��\r�2F˧4\$�\n�~�;Pk�/���ǬЈyQ���懰{�Zp�`�S10��W	�o�N\ng�Ak0��MM�z�{���	uq�����\r�쵮fb�{�Y���Of�o�o��h�1�`i�	/z�fX�bX���	��1��M�HhLX��`�1��9���..J�qo�LŲ 1�Y�]\n��C*ጠ:.dT�o��()B�\$�\$DV�D�tc�-e�Ab%��F@�%�8�p�\$X<�l��Z?%2����jPl)G�@E�D� ���\"� 0�	�G'�F�Fώ�5�A���>��������QI�Sm�F:�,k-p�=��\n���&7.\r�d��i�Q%rؾ\r .x�(� dE�13�	.�����S %�0y�䍳2;�JJrf�%��=C�8���@/1�\$ǀʧ�d�.�1��AmD(�Q��S�Ni7�}���.�\0֖ҴcIS9\0�T��}��:�;2�:��d ���u%�2\"hj�_-r8�\rs�4��),�>�=r�8��2�?�;�3��@�G��\"s�8QA1�\"��7-��#C<ӽ@�̂.C*tApMCC��m r�BN5D-�\"s�D�.�P�.y�\\'�D �0{g�|bGASA�CN�Hb���D��Gn�c�S��)�e�u\"�7Q�{C;&D��H3t����E��D(SF21ª�L��4p7M4������3o�0�t���c1�MK��+��L�/\"e������!�%N��L@�`�*�>���sJ\$��Ð)ú),X1,R���(��\$�6)B�/���t\n���ZmP�%!����.����W��W����t��-0h]WƸu%:��@�e,#��#��b�~&/B�N��T'I\$45�P\$���SӊrD.;��sH!ET��XX�\"5_k3MR?��k#�=�:9��C�߂��2 @ޏBc�+b����Z�/�cV%X�0(�h�62��GM\"	v#R�p�aY2�f#R���,ct�#�n��YVaȪ~%|S��\rp�T����V�.Pcne�Gthʼc �x��O�n(/�2��M��k��0�����pH�3��Q*�-�bɘ�T��H�d�C�S1DӶ�3�6i�in�?\"�R���-@�{\0��@�%�";
            break;
        case"pt":
            $f = "T2�D��r:OF�(J.��0Q9��7�j���s9�էc)�@e7�&��2f4��SI��.&�	��6��'�I�2d��fsX�l@%9��jT�l 7E�&Z!�8���h5\r��Q��z4��F��i7M�ZԞ�	�&))��8&�̆���X\n\$��py��1~4נ\"���^��&��a�V#'��ٞ2��H���d0�vf�����β�����K\$�Sy��x��`�\\[\rOZ��x���N�-�&�����gM�[�<��7�ES�<�n5���st��I��ܰl0�)\r�T:\"m�<�#�0�;��\"p(.�\0��C#�&���/�K\$a��R����`@5(L�4�cȚ)�ҏ6Q�`7\r*Cd8\$�����jC��Cj��P��r!/\n�\nN��㌯���%r�2���\\��B��C3R�k�\$�	���1-�[�\r@�Ą� �T���T\$A#�2J�D'ҽ@PҀ��J�0�������2t� ��j���|�A���A�ƃ\$:�C;�#�~:ְ�A\nC X� �Ό��D4���9�Ax^;ہtmU\r�8\\��zP���2��(��@�ˢx繡�^0���������S3�>�9M��b��ılk��+�� �&8J�9a�p�7��̺϶�P��HpΊ� @1(H����bcx�:�1�=�LNt���p����r2 ؏���k��2�c��-�ܿ���ǈ��f�@ӱ�(���xk8�cf�V\r�{���L��F�;b�9���U!)�v���kg9�Bƌ[ؓW�z&�\r�x7)\0�(��S�C;�����[AC��m(u8��9�o����]�r\"����PeB��SZ4����(	#l�8�(����V�׬(_u�%\"OO{߁L�I(j�3Fҥ�8�3�] ���2i�*\r�zQ���؇0�Ձu.�Ie�(\0C8a/���E��((`����J\0C\naH#A@�P�YH��M���K׳V%����W�C�2�%��ps�W̛��N~\r��%�y]*��A�[Q2IV,�L��P@����ʗ��_�����MQ�aYh=\r!�ا5��4�CŌ�Щ~��u,ŝ��Z�em�վ��\n�\\��s�蝗j�y����x��x I��7�ךH�؞���N	�\$���BH@�R�9���d����XM��yG�s.!�;D(��4\n�ka�}Rq������jE��hF)̔ �@hD/M�O+B�H\n5��d\n\n�)\$�y���G�\$\r�����<�#�׆s�GU��7����3�u	�IǑ��	�)\$�(�3��Q�(%��c�q#��\$TW1�HP��6�N�!�ɦZ��\$M\"H(,���\\���ney\rb�D�?���������eB�<)�EyI��uu��3�g9V�]�ԅ��|��MA4����ge�&9���:d�ESy��.aI@ލ��b.�y�!����p �Ru��\\Q^|���Ƣ�\"H�Y��\$�ɅVj\r�Qrf�;\0���\0U\n �@�k��D�0\"�e4a��g���	`�Rl��\\�xpm\r�ŵ��KM`�e�0�@�JV ���Y�#x��i	qPY��s�Т����^�/=o�wF6���Z��\$Q����:yb�8�7h\n��3(D�̾#R\0PV �x��\0u&M�V���97K��e@'�\"�Z�F��� �F⨎39\r����c	�ȠSR)}���0��	�).�k����4���(^�.%A�;�3��Y�'X��1�	�7v&8��,Ю-qu���(���\\�Y�l�X�;D��)=��8*g*��{��Cp)d�9�\$��ɏi�r+�ܗ&׃�\\7SD��D��,KR\nH�[P�BH\r̺���Q�py�@���2^kڲj�����2z\r�͗l�A�n�Jeq��HҲ�A��D3&7^�K9�[�m\\/�~��[?nm�fEg\n!�XՒ�M��a8f�Ki*@�I��m��0���oV�f�j�������I�j<�prp����pk��)\$ա�1�,�����P��R�n}kɸ@Dd�(�&U	�-q\\֗��Co=!���rS4~�M��DEA��co᠛���Bz�Eը��0c�)�V�O&n�hI��>���m�f\"�aWe|��N��/h=��c��ۘ�����\"W�n+�7ِd��K�tAF�/C���!6	��܂LtZ�>~�O���9���S:E36oA^�n��:��U/�-�\n8��\r�ߥ������w�\"4�g	Bb�7���&���_��ԫ�w���\"\$��o�,���/�w�h�x˟�>9�</{�q�7S�ݸ\\���܎����/�|�d�m�4+B5\r\0�fT*æ�.��o�5-������P(���a0�O��R9�v/+�Mb��+���6;�I��(�f��F�,/�jD�l�\$�� n\$gK�.^�ⶭ͜�� �P`J�M�A�\rG/�P���l�����r\n�� ���q\r�o�\r���Ϧgp%�(0C�Ԏ�����(��������\"[�L.MR��h�Dk�\"(�p�-��Tb2�)(�Ì��b(��0�-A��O��1D:�T��F�qvK\$lNR�j͊-EǮCQp;��G�n#�k\$2P���Q�aTh�W�&�q����_���p�J�0����%b)\"�O���{qQ�g�\n.��Ѣ[\rr�Yc�[����O��!r'\"g�iC��g^M��� �#D��'>yE\$2@�0��ɀދ�r�Jb��'�f���������pm�6O,�Ijf��e����/o<1�hX#7���N��x�h<@�j�\r&�HD���^B3����N�@���E�,\r�<A���b\n���qF�1fT�O��k0�(T�0��S\n��ޛ�#�xN�����\"%w��1���v��!R3\$�ȭ��\rrE�� ���ūe��x��	�^<�(H̡tj⊽�����K��c��� @��1���N6S�CB�'o�QC~��6P<0�@j(�אJ{,�:D��;:�|5��z�\0��UR�B��<k!\r&�6&�L��x��D�����τ�'�p˞QЀ ��]#�#\$��:�M�\"�\"m�B����N/\$�1�4.�T�R�S�u�<��O�`����r7\0����*s��M:e	(p&t@�:`";
            break;
        case"pt-br":
            $f = "V7��j���m̧(1��?	E�30��\n'0�f�\rR 8�g6��e6�㱤�rG%����o��i��h�Xj���2L�SI�p�6�N��Lv>%9��\$\\�n 7F��Z)�\r9���h5\r��Q��z4��F��i7M�����&)A��9\"�*R�Q\$�s��NXH��f��F[���\"��M�Q��'�S���f��s���!�\r4g฽�䧂�f���L�o7T��Y|�%�7RA\\�i�A��_f�������DIA��\$���QT�*��f�y�ܕM8䜈���;�Kn؎��v���9���Ȝ��@35�����z7��ȃ2�k�\nں��R��4	Ȇ0��X\r)q����\$	Ct9����#%�څ�O\\�(�v!0R�\nC,r�+��/�؈ϸ�򰘦��ڄ\\55��X漲�ȘϱH��> �ئ���K6�I%��mp!A\$J\"+�+3b`޿��x䞍�Z#\"�P�Sp�A�@�A�ƅ\$oH@0Đ0�H�ꒀ:�0�G\n�C X���ь��D4���9�Ax^;�tqS�ar�3��X^8Bv ��J�|��n��3/����x�8�h�<:�ꒋ�����-�:�� ؃SZ�#xV�4�>'\r���Ҽ�Ю���J.��\0�<��NI�e\r�!��`��ǉ_1�2���m�裂\r���j�ƽ=������2э���2�6fa��V]R�:ƴ��\r8Ǆ2���!a\0؀�@P�b�L�s���S��Oj�F��\n\$���Ѓ�b:�ؔ�S{�9�i�oP*���&L[�>�c( �h[�HD�s�\\��9���2��Lv\"�|G�tM'�l)��6#���\$���(\"�7եɇƁU�C��\" �vw1}_��͵Mb^�\r�0�60+�0�I�ޠ���Bˎ��n9��^�M�;��#?��qd��`PP�I9\n��5��@R˸;7�RAp RĴ����Y//&Ļ3��y�)�dƅ\$�\$o`Q�?F����8 T�p9,v�eʣiY&�f,���I0'H3�U�I�,���#�±ЂC����v�Q�����\0��c�b�B�X�ECF��Bʈ�=h�5�����|�\ro�ܸC�(N�At.��\r�&���8���\n%�������8���pf\"�+G��0S�k��2p`�������W��P��{%!�4��h��]�*�HO�tG'D��g�hFi� 0�Q�D�A5�@@PA�\nm���\nIQfFfH9�BI�b6:\n�1��rH\r��6̕X�����?�x��DR�p�p�AQC�	9\n'Rf/�P�!F�S\$�#�lޒ&����2L�7&�FIAH�y5h�� �,J��7��d6i�D;�s��6ֈ\$��\\��2�\0�¢�1JH��u(:V;/�%��Hq18m�S:Y	�}44eL�7�~�b�V�t�=�O��\r��[�b��LCs��@���'̔�+	-Oϝ7D���\\I�1�'�2*�v����xm�8P�T��@�-�#	�\\��d��Oo�@��1E#�3|'���(�!�jf�NQJ<fc-�YQ��e��\r!�%ҏez�tK�\"P�,U(��տ�Hh�J��qY|�'�z��\"�!�ߗF�S�f�s���4��C\$�}�`#��[#�`H�n�਩G��ja)�8dh\nSO���r��J¤�Q���C\r'F,Ƨ����0x0��Yꗕ%�3oU�+��C�9��2f�t�aC(wAXϖ,fcb;b4솕h�Xi%˹}���z�)�pNuL\\��}��;���À�I�}�09v�rΩ�/���O�j0�\n�z�2�7�t7�#�	;@\"��_�`�Ҥ\n�P �0�A}Ao~�*�aA�#����X/-,�ѱ�Nʙ.�O�������6>XYc��I�ɐf=�P\0�a���d�F(��݃��2D�{M���6F&�e8�����y9ep�c�7PJv�cۊᣠ�Z6����->�`�7~����~ֹ��̇	�jd�#DH����T���v�x�st\\t��3��V�+hH��HG\n�/�����R0�1b��	�\\)��B��ۺLqH;�,�04L�87�1(���0ř}h��Ƽ3�@LO������ऍ��'\r����=���,��S�1���ׅ�<fV@N�¸�\$��*{�z������18a�1�Y�d%2��r���EI�����\\��95��iÄ�\"�����aS����_b�Z�����q�������P�zQ���up\nUd6�'ޫ�xԥ������~���?�?����{��=%�v�<����+����\"(&~�k����x?���u��͎��\"x2��ң\0���ۨ4N�4�n�N�ڰ�͒�P���7\r��I���6�K8J�����L�K4�ȫ\0oh�J�l���V�bfco�����v���E\$\$�<.^�b� �z\r�G)J0�\\DO��\"TR�_Ϝ�e%��9�8�M�\r��E����p���F�p_�v�/\$�l�?��2Jsk���?�ʌ<�o�Pi�T�q3�v�k���\n�N��2�pR�\0�Q6��\0�X��B�/*�f̼:�4TB��^h*,!Zjm (�^��H#�L�\r���BP�6��֑���Q�q����\0�Pj�r�f'L\n2HM'XCq���,�Ѻ(�`��.�@o��/����&�\r���I��I!U�\roʰ\0�����W�o ��Gq,3\r��\n��?\"�C��g9p�f�,*fl�@��L�\"�J��Lm2��s\$�V��\"�!RJ�b��]%Rp3`� �mi&�@QHG,H�KJIuĭ%2�Ir41��K��!à��r��}&W�G\0S��\r���ƽ�1\"�,�fֹ�\"d�ʒc0oX2�V*CC(ڒ�/�.���m�.#�\r�V���m,\r��]�9�4\\�Q+���' ����C�1�ܴ���Z�h�\$�\r�����k�r���-��6b�-�0�<\$DN����j�V��\"GqV�\0�K��b/3C�*lZ|\r?C�2k�ݰ�\r�Xq�N7�,��4���Rc�J�5��\n�S��F(˺6���~�'7�/�1 �fgD��?��7��Й�\0006�q��Ǝ��`4�4��*04��5�^;�\"?���*,pB�ƣ���D?<�,�?�\0Үf�B~�+��j�o�ĹF�v�k8O`�4s���V@�DF�D��3`�0,Z2� /��3d�3)\$�2���&%����'&`�q�\r�����Gn�J�6a����/��";
            break;
        case"ro":
            $f = "S:���VBl� 9�L�S������BQp����	�@p:�\$\"��c���f���L�L�#��>e�L��1p(�/���i��i�L��I�@-	Nd���e9�%�	��@n��h��|�X\nFC1��l7AFsy�o9B�&�\rن�7F԰�82`u���Z:LFSa�zE2`xHx(�n9�̹�g��I�f;���=,��f��o��NƜ��� :n�N,�h��2YY�N�;���΁� �A�f����2�r'-K��� �!�{��:<�ٸ�\nd& g-�(��0`P�ތ�P�7\rcp�;�)��'�#�-@2\r���1À�+C�*9���Ȟ�˨ބ��:�/a6����2�ā�J�E\nℛ,Jh���P�#Jh����V9#���JA(0���\r,+���ѡ9P�\"����ڐ.����/q�) ���#��x�2��lҦ�i¤/��1G4=C�c,z�i�������2���t�̬Bp���\n��0�B�1T\n��,�7��p8&j(�IH�(���i�/�����㒵*���#��&��û446Vz?ģ���X4<�0z\r��8a�^���\\�)���/8_I��p��xD��j�/c2����x�!�����Έ2���P#���U�h�̥�C�� �`WY.N4�.�\"ɍ����\rb��AN�J+��r�3�h��DcC��c~5�BT0�����ق�:�\"a+��\nC?1L�2��0ح���L�Ӣ# #Z4�C;�\\�����K��70�A°�[��Ƶ;����3�\r������E��\r��o�zä(��\0㹎C�ƌnG9��M�\r9SW�6�wy�z�cE9Vo��D!�8Ί~�����<�����t����\$��:\"�o+��oVn�L���H��z�cB���H��A�#j�D`�)=	\$����¾(��2%��1C\r���.E�\n�F(ŨC�@�@s\$�!�0��\0o\rjH�>BC���<(�f��E���k� f�1�z\r�)�	-0��&k���W�0 ]\r�u� D��|K�ى�X�\r��!\$-R�q�\\�>+�3���]c&F#��I�MB��o�4L��JN<0�0�5Իr�^K�{/������\r��<#�w3|��ʧ�A�Q)���xb{a�5D*��,/�29P!�19�q�Q.We�8���V\r'p-�J`��3�I����#tM+b\rA���9���?ģ�a��}R��)�!)���+,چ�\$o�Fd�){1��cࠩB�����Z�܆�v�[�)�%e�6䚡G)uR@�LW��DH<6��N�HX\$�&bhM�)G#�t���Z�\njh����VH�kR��E��b2@3�\\<�\$.ZZ�4.\"u�����-A�z,���\rB��#}\"J�a\rE(�'��e�h��Hv˔O\$��r@�U�혢�^A��T���%�\\BҢ-�jIM\"v���MaJ��R��9��M��rn�`F\n�@�U�l�%�bO��9���^QR!�_���KQi�f'��0�x��(L%�������L>VV\r���MXő#����,}��oD� #C9ק���}OV[I	���`AS�I�?g�W<��h�Y{n0 2UZa/�VDp��r�u��*��+�_n~���\$bX�Z�X2��+#y�Q��@�X��4T�Բ�E�R�\"K�H��IDb��LU��9���Â5}�r�k���:I��d�H[�� a�����^��/gl�%l\"����Ǆ��Ӹe�l��8P��K��0���7Ǌ�OQ{�z�ɐ�xpx��D^��|OS�&Sܙ��J��KX&*Ӹ��ٱy�X��Cv�L`��8�\"� M�	��dv\\\n)�.U�b�ɡSa-o-��HI�F��\r5eלu�*aԉ� �@�BHu����	V�b �!�	�F/遬ť\0^7�=q������ύp\n\n.�	�&2��䄻������]3<��P��̏	*;��p���J�����YY\\��8��/���K���1@�~�>*vG+�	J&D��8¶��i�v����%�e!��\0ʉS1+}1(�_\rH3�!͛yo��B��^u�A�9a��e42q�wi������\r���HԽ�v�G�VK��j�0��W�x�	�r��d�y�=fo���|t�Ȭ�S�f����gǚ5��A�&m�A��\0P�F'�y��q�	o�(�Q��0팧��F�u۳\$�c�g,�%�%�n��/v?96��ʑɴ�����c�iRA��9�C��|U6J�6\rX�-E¾�{ٝ���C�_��eKN��Zg+�O��ք��r����I��*���n��Ǹ�/t���r���(�ob��g��q���,z��z�?*���f�´��X9��ʑ�n@Ex2��0h�dc�P*�J��|g�t+Px�P��Nt�-nA�d��50�5�)p�2ˀ�j\\ǫ�:�Z�cG�X�0�?p�y`p�Ӑ�=P?�f��z����i� �#��P�*���>M��K�b h<<����,c��]#\\\r��c�;((�\r&���( [����nU�<��\0%�V�-&��8��1�3���f�sP��{&]pf�P]Q����Q��P�ͮel�,�p-�RG2 O��L0�q��E�0wo}��\$Bm�per�c��1��B�𖎜\$��\$#���@�;'*'�2p��:��8n��gpp-�\$�	�'\rp%\"��d�1\n,#��j��`�玙-� �eC��:J�@4��\$�5&g�&�kO�&��\$P�/&�AV�� ��Aw#��҉(�%)�#L'��c2�r�)q��{%}*��+\")Rp:��,�,ĳ'��\rL2���8�6���].�\$}�h��no�\0!K�d����S��Q���Hr�>S,\rS0EA/����VT�M���@ľ�ͷ0�3.3DT�K1O%\n\"z\n�F�D�4��'��B��̒D��J\nz�<� ���@P�?e��(K&* ��j�9�1s�T��6W�g�b2\"lj7�g\no�1���n:�i9�ө	��5�XP �j�w���p�mJ�\"&���1�H\$r\r��&�\0@\n���Z;i�\\i`䧦����nkB�M	#�|�6��4#�<\$DC��Mq4F/�&hvD�ʤ|C�3G\n��#�N1b\\�sܐ(�sf&Ɋ8\"���A(�O,�vR�Nif��U�n)F�UBN�&��U�葡K�gO�2��h�O+O�L�±�Z�o\\g�:j�)�xt�M1�.5�7�h6�&��\"M̸D��1De��X\$�f�c���D�KF�k��'5�6;�<���oBb:DDt @�&My>\$�Ũ���	��QF_\"��c�j��.��\$n3��7�4��\$-�#��:��I\"@��X&���(��+ �,͊o�;����mMsB	\0�@�	�t\n`�";
            break;
        case"ru":
            $f = "�I4Qb�\r��h-Z(KA{���ᙘ@s4��\$h�X4m�E�FyAg�����\nQBKW2)R�A@�apz\0]NKWRi�Ay-]�!�&��	���p�CE#���yl��\n@N'R)��\0�	Nd*;AEJ�K����F���\$�V�&�'AA�0�@\nFC1��l7c+�&\"I�Iз��>Ĺ���K,q��ϴ�.��u�9�꠆��L���,&��NsD�M�����e!_��Z��G*�r�;i��9X��p�d����'ˌ6ky�}�V��\n�P����ػN�3\0\$�,�:)�f�(nB>�\$e�\n��mz������!0<=�����S<��lP�*�E�i�䦖�;�(P1�W�j�t�E��B��5��x�7(�9\r㒎\"#��1#���x�9�h苎���*�ㄺ9��Ⱥ�\nc�\n*J�\\�iT\$��S�[�����,��D;Hdn�*˒�R-e�:hBŪ��0�S<Y1i����f���8���E<��v�;�A�S�J\n�����sA<�xh����&�:±ÕlD�9��&��=H�X� �9�cd����7[���q\\(�:�p�4��s�V�51qcE���!�x�-�0�X�2򨑉��_!���h��K�#*����P#fB�/�8���rZ����(�f��B�6�#t�0LS\$�4MS`@0�c�w��>w0K2ܻ/��H�4\r��0�p�8NA`@j@�2���D4���9�Ax^;�p×�2�]*��}��Z��2��\r�����Ҩ�,�px�!��Ȳ<}���Z�:T�l@&.#	�xd���<!G5�YZDɡ���lMʿF�v��+�X�Y;�z4.`�0�Cvb3�(�էI��y�������Ľ����i~��Kʟ�!�ʇH#\$��)�e����E5V�r=;ԣ\$���YT{];��|�!4��8�)	�>)b��q���{�j�F���D���礵IR�3Ω�Y\">��#\$>���e4���o��!�-�ejȗ˪|���Å���Y�=ٟ���Q	��ݞt H�%1%-���S�%B�Mչ�]�jAd��\"m�Q���3����w:�Ҟ,�v}'���	ln��q�=s��I*ʂ�x��xLFEi�N��\$fL��t���h�X�!N��cQ��@� c� #*�ƕ���rH��`�%\\Hϙ�X&�	8���*\"7�-���C���,(�+%���1�Ug���3HHq*�X�\$d��Jd�<\$����I�!��0\\W�Q�J>�4O\$��DD���@����XN6����%MN�q;\n)\r��F�9�M�\\��3.}��?���&2�k�ɳA��álf�N:#9���(3�����(�ϞFƻs�G���@�4�b�R����#�C�	��Μ�\"rIHdx%H��(�W1��^�q@R�%P�1�e.��0I���h)-28��@hL��3 �Ճ�]ᑙ��@֚�^.@�&��q*�RФ5RO�-��O��.�+�\r�Q��{�M<=d �L�S�3\"�����E\\T;')����j����A������+gm-�¶��܃sr]k�w���Ɓ�;����sE,�>��0��թ�1�����(���\0������S����Ȉ���1���T�9(;O�q���/��0�AI\r��\$��~\r���̻+�s����`�`o�����Z_[��6�cu�!�9�S�yR9'Q�u�+c���)�&'f� ����B��33&:t��y�\rD?n�X ��p��Y�c�\r���Hv\r+x3�F��\"�L���ᴝ�21\r�gAW���Ϣ�wR�X0�)S2��^�����r�͂s�nL�C!�P�����ȉ���vԣք�Qʔ���J�D;�,C�����ȕ)���*�m6Lb/�h�d �Q,;D�(\"V,#m��e\r�[mET1r���[ɔv�,�5��FL�k��0����(�\"�m����2��*{�%�3�4DU�������\\��\0֛�����*�d<��B_=%�/&��H��q\nl��բg��2�]ے�w�o=��Q�cD���y\0F\n�A`vW����C�F߂����(l��T�&�U�;/t3�ft`�h��k�M���G�3�r2'��\"C�SǦ/��/a�߿ȟǏG @pܦ�H}NjQ�uZ��U�DP�����f;�~��G\\s1���;iT?�D܌����O�O݂�YR�x��.�Y ��6)YP�4�\$im�%Bz�*8O��K%�,���|��ɡ_n�\0����ߦ}(ާBH�7#� ܲr��q@߁ (UIm��ou�e�]�y�َt8o����Ǥ��\\#+�_M\"e)�����a��?i�v���#ZZ�\0H���h������Z�`����e�ahd���)G�輅w�Տ���L��uN�t�i�\$�P*zP.�H�'�ꇂ�/�t,�K>�BO\$�3�\"D�ʘ��Y\n'^Z/(�D�Dl���*�L��U�/H&-��dh�Z�L/F����.Xw%7\n���P<���*����#%(�	&�2���1��J��J�	A)4�HA\n��X�V,O���2��(a\"X��f�`�\n�� �	��G�8���J2�H�l\$�.�B�X�FP��� �\"G��y�T��@z\r�Aq��_g�%��ۧv�E�az��n|�0	���0a�G��q|<q����+�����b�.'�E����܇O��!�،�nF�1�^�j�#��h�,h���{ N�(�-�\$I\$�:�j�=��\$�z��0��FF�ڭC�%�I&2!Q�C�T����I�(2w\rRz��WhȆm\"@��A��\\&%�\\/����e%�(��\$(\"&\"YΈR���.�!�E!�fF�?���j2��R��R���-���I.�M,g�(�>C�+�vd{\\�.)��{�އ�O̸zJ�`P�!�'\0�>3�J�ChN�o@0o5*�.w�R�+�F�c5��eDP���u�.��bN�����X.��vUZ��Jc�/�.���9,v��*�����:'�:c{:������6b6����G��U%Z�S����~�^,��'�Ө\0t(���\"04b���C[@p�<�d�\$�Ϡa��b-8\$/��=	�w����G�UM�C������D4��/	Ao�F*ro��l;�6t�bb�n�ɱ���d3*2(9'��td��iH��T@�f���Ft9�1��6EF�H�9�KI?I���KF6�c6aCL�a �L\0*�X��3�&��Hҿ&q28'6\$�&S|AԬ=t�\"t���F�EN/=q�P��� ��7�SJɥP�I'�N�gN�Q͹R��'�j�EkF��S2%Sa|�����\r�.��XR�p*1�VB�V� �NƧ�wM��U�'��\r�qXԙ�@�\"�D�jBb��;H�.O���=�Ut���Z�\n�&&�g9Y���rGg�S\"F�/\n��\$�[�U\$k*),��1[+ c��:�P��;VdAVՋ\r�H�N~���\rGc9H/��G�,��W3s�G4I�SSRq&�d��eC]S�6%Z�ee.���mIԕV4��Iggc8�p��ZY��g�G6���Ki�we�_e�jV�8�kN��/4�f� 2�ZV�i6�I��<6�k�U<� ��|{�V(A\rn�BBЇO6�%i.�Q�'��SG/�O�\$��=f�s6wOpyqv�P��;W#9w&@w+ZV�GV�;2���S��P�d�I����v	;Hcv�A�qR�c�XT	mc(���tU@AW�t83Q�\0�AUyi�{M�IC�L�Oy�u��7�=סzB�{�!KJ����W�R�7K4�u.�tW��ח}#�mV�#�zV��\"o~�h�׿my�݀?=�zu��<I0#�x��N�/tv�{v�w3_�p�3��(��W���\"��p�!u�~4;W�QxV-5�hۃ�'/�0�!xW�1�xe,T�t�%iLm���u:�W�Z�;����[e�N��7�}x��W<������-,Pcu�x+�Eȓ4sz+sNx�rU)OW40a�UU����wv��S���Qyp9\n���<.��\$c�D�Q�� +H��	0�4y:�]�� �]��]\$�P�YY�aUC�)����4ir\r�VR������ºޡ>�e>w���اоb�YJE�&R�\r[']^�AX���LS@�\n��0�*�]�q+��Q��(˝HM��/y�Z�\"&jD`y�`D;�9lx��r25�/�E\r(��\$���5��ciI�d|�7�pL�VO4��NZ+\nD0������.j����\\�\n�ٗ����A{��D��0��+(��\n�IPI6��s�e��Z�9r��*aX�:��_]*��}:P�ݥYJ�Ybh~cL.��c�;���E��=�z&���q�#c�s�����zC��;z��̮3������Z�z�=EY����y~�.//���q=3�9I=�=����@��\"+��_��\0���TC\n�Pz��>�t�k�GXvr&:�� �y��\0�0o�8�Z(��[{���b���2gde�d:4[������RA�6t����p�/��	X�;��B��}�̀������� &x8�w��<�SJ�_x�a�@";
            break;
        case"sk":
            $f = "N0��FP�%���(��]��(a�@n2�\r�C	��l7��&�����������P�\r�h���l2������5��rxdB\$r:�\rFQ\0��B���18���-9���H�0��cA��n8��)���D�&sL�b\nb�M&}0�a1g�̤�k0��2pQZ@�_bԷ���0 �_0��ɾ�h��\r�Y�83�Nb���p�/ƃN��b�a��aWw�M\r�+o;I���Cv��\0��!����F\"<�lb�Xj�v&�g��0��<���zn5������9\"iH�0����{T���ףC�8@Ø�H�\0oڞ>��d��z�=\n�1�H�5�����*��j�+�P�2��`�2�����I��5�eKX<��b��6 P��+P�,�@�P�����)��`�2��h�:32�j�'�A�m�Nh��Cp�4���R- I��'\ncʳ\$��s���@P��HEl���P��\$��-��64�ba?����*NMM%4�-N���P�2\r���A0[Gp�'#~9��p��ה��)���:\r��B�D.9�`@\"� �3��:����x�w���r�Ar&3���_l���^)��ډ���̉�c�\0007�x�%\"��)9U�*Џ���<3`�5������Cs��\r	���V�#n�(�'9	�4ݍr�����R���5�N�� ���h:Z;!á�](�\n�`%�)�BP�\"�քLV9�(�+\\c�6A�p� bC�(�ë�1֢ϴ����%���CX����z�P�d\\22@P��+C��&%֜����Y>9�׾J���65���9�c܇\n\"e���������<�m�xɽYk�����Rc�J�vE�b]��T�^��/]�ۭ�p����1�J�H��b(��>~�~���e�U�!{~���C����7\"gJI)��3`خ�HO��P��&y!=a�<���V1�a���@]��M�t0�p�]A0h�ͱ�PP�I)	�e*Bt/Ma�' �!�0��p �GX��2��Ðmm��Fr`�T�UZ��CeT��;-��f��)\n������J�V���-�X��K�\"��.%���\"�\$�����XyQ�L-��w�\n�V1l���h��U�ᄔ��*E��h%\\8�ІV��m�s��ֻWxw^*�]/U�Cr�	�?���_Pp'\r�D�䚖�� ����\n���k�)dHȾ�I�J�I�� �Q	�7')&5���XR�Iմ(���jh!�3\")<#���-%�k'1M@�1�v�^�b22�쾙�xOhd�\0'\"�H���� ��b���dB'!�1zt�pz���0@\n\nt9A\r@���!�[d4��2����\rY�A�Ն��a�<@�nI�jH�\na#��!wn��Ɯq\0���^L`�f�HL�N(J��!h��;���C�8���L�5����*h-̬�\0̀�s��!�2@��BB��\r;�xI�I	((���/�����hK��'J*���Hڿ)(�30�qDhe\rU�2�k�\n��A����E�7ka��L1DX#@�pCsA%ϲ���k�?Ĥ!�P���k'�B��&r�ɢ`('��@B�D!P\"�K�(L��ӐC�Tv8�`t(IfA%s��ХP��E��RDx�üx�Cf����[FĄ�\n<rn̢[1��9�AB����T(	g>�M�Js�d�\n�9�2x���=@���:ɧ�B�>Z�z�t��l\$��bLi��X����@P	]\0R�CD��N�j�JU��Ps6r	�+�U&�3�vJ�*`apiI�>�j���7D��!��`ɱ�J��\$�=��'P!��)��\\Ԑop��B��n�WI�L����Ƴ�\0005S4\\O�f��xt�;��u�װF��<�HrIʏb͟�T��vMz�'tci1Y5�(#0�����%dI8*���~.���M���*V�P*���H.WłeH�a���0_�Yh ��4s2�C[L30�q�.PZ�<n����#*��o'X��vh�!�(`\\y�=��1UnS� k)<� �����mP��;�I�7>����rCѪ�(�<�+�R^ha�|��=A�f�V������i,@h�'�s~ې`i�}���ջGp�ᠦ��{:���&1�+���\r ��R����E:�!�/���\$�l�2B��#*E9vz�D���\n@4���J��F�D- b�b���20�F5z\n iY��\$�����M�H�#w*a�/�9a�𦲃�Y��&�d<��]��V¸�*gr�w�U\$���3��ˎɻ�b�5hm^��,#l������\\(�����mZ�����\0�r��~��I� 7�\r���媼�-�~I��@\0�F\"\nBqk.9jB�H\$r��\r��	���7@�f@��\$I���:航d��(�I�b� �f@��&�j^�A\0SgJ@g�yP�P��%G� p\0��J�B`ǐ���D\"�p�0��\r'�Q��\0M-�\nm=�\r����{����=#�cCm��E�&Ĩ.�Z����F�戬��o�t\$��q*&.����.�n��Ι\r�q%�J1R����o\n��^``켍��+�C����l������q���1��ZE1��]\r�|�e�`�\rFjIB,�lj`�G�����*�fI�6&j���&f��}�s,�̂�j\\�qkj�m���Y�6wP�IQ �B,�q�F²��O��ٍ|Sj'[\0�r�,�Æ*+1�j�R�f�v���&�L�rjeRb����z�\\�Rx:��(B�\r�q\0��ݣ�)��g2����q�`��9〼Ҟ(c&z��(��*ǈ/�@��keEƮ	bL�!�R�T�Bu�:JHmqeQI)M+'0%/�#����0��1��mh8Ho`�fS\"�3l���2gO2��~go3/0|�Ji�LI`�Q��'�W�� �E�\\u�1ҵsl'3q*QYǚy�S7�_3r�9d5�}4��)p�&��	Q�7J[(�x�U4#�-�.m�&�)��U��Wq������̟1�2�����;�]>�\"J�*��� �R�.'��6Q�@�!}rg;M�Ht�@�z4�\"m�1\"�1(r�zI�1�ڃ��-�h�X��F>P2�ӔP@���5�J�/	F-�o	b@�ch�~\r��Q��D�+E�2\$^��B9	�'�G�M��`ց���έ!Z8[�@\"m8:D~Dn�2BRˣ�\n�0����Z�Eb6<�ت˶Xp�Q|��El�O�%\"q{��EbCPƞ�.���0~�**�B#�>����&	��Ĝ\n�T�FI�^�2R��J)#dh�CP~��2\$G\".wU#�#�	��h&m�J�|/��j2s\$�4JdJ��gsR�������7\$�Ȣ�6p\r�ʋ�\r���+���06n�Y\"u���cP\$բp5c@'�܈`�W-C5�\$�X��aT`�&+\"�S��D�:�V\n�5*P�#bR	��\n6W#T5�t�FX���|u��(q\0�&pie#\ndTM��9#|uT1��[B�Uu���]	kuXp��Ug�[-���6��5�D�gf\n�vC��ĸ!��	\0t	��@�\n`";
            break;
        case"sl":
            $f = "S:D��ib#L&�H�%���(�6�����l7�WƓ��@d0�\r�Y�]0���XI�� ��\r&�y��'��̲��%9���J�nn��S鉆^ #!��j6� �!��n7��F�9�<l�I����/*�L��QZ�v���c���c��M�Q��3���g#N\0�e3�Nb	P��p�@s��Nn�b���f��.������Pl5MB�z67Q�����fn�_�T9�n3��'�Q�������(�p�]/�Sq��w�NG(�.St0��FC~k#?9��)���9���ȗ�`�4��c<��Mʨ��2\$�R����%Jp@�*��^�;��1!��ֹ\r#��b�,0�J`�:�����B�0�H`&���#��x�2���!�*���L�4A�+R��< #t7�MS��\r�{J��h�_!�\\L��LT�A(\$iz�F�(қ0��*5�R<��l|h ��J�.�����?H�~0�c5��8@��/��� ���h�\0�C\$&�`�3��:����x�a�͵\$������{�9�^)�2���246�#L��|��k�(��âZ\nx�0�I0�3�� Ĵ�h �%�O\0�ˌ�%�~.K�촉�|3}R2`+�eB����Ę��N*b�R�b��Ӑc���%C`�2�`P��B\\��c�����-�<	�2��Z����6'��:�W+Ծë�1�sд2C��:N��\rj0�'N%44�+#l����&A	\$h\"\r�e�E�����hz��63��(1�n��ފb����89��v���6.=_*�\r��*��\r��s�';nd������;\r�+��D�\0��`�����bM}ƃK���ZFl�1��3�Ҡ���%�>Yp��[�p��LC�;O��8@�-�&�c\"�6��\$:�!@��z�BA�����b���p ]�\\�B���mh�\0r��Y���(BnC�+:�x��CyL�3ʁQ)��PcL����hcH*�Z����G�*�\\�3b�@�,\r�Z d�Q�-ش(e��	/	�Ā�՞�U�_E��\0��m�ڽW�a�u��Kr�YK07,����m�`�\$���e��t�`�/\$��С�[��3��Y�Bh0�s�Q��&ሒd��;m�f��F�*dU���Ȁz�BKB��3ER(����|.�ES�a�%�\0�^��i#�̗�\"\noO��CX�?�&�i�\r!�\n̦#`H\n\0��RGI2�v�7����\n��:�Fc0P���lҟuF��xw�Ԝ�B�d�i.&Ț� uє�2G�q�b5� RA}\"D��,E'�\$��\n���\"a�Œp҅��\r���\nZa>���MR�!��*x�=O񙂏؝ɠ��z&�@'�0�\0B��\rG^�@�CBIG]S\\��f{G��#�)M�6�D�!h,�eF�` q!2��B`	�F\n�l��D<��?��)C\"f��\$���V���hhI胮�慍C|��s�T�pl�u�L�88C�/O�Ƶ�V�i!N����Pn�\08&a@���y�4�f��\\*\\�>6&�O�&�&)f�FJ�J.�Γó��Ϻ����L��Ɇ�b��˔\r�;.�d���;�������4�B�ޥM��Rd1�H���}�\0Od�x���%\r�C\n�˜G���<v~	�)�fX@XZ1���y�c�[�[�H����ꐡ���vI�\0_0��F�&���^捑ng!��24��\rNM}�[lLL�0ܐ�'l�\"q/��ۑB	�bq]���G�[��Ӑ\0�c�r�\\�PF[d����]��!�<�ٔx���3��8��C	\0��4�����}������xD�{�6��0��2MFIZ��0��2�I�t��b�:HA�4(bn��\rA��C����Z�Õ`�uֳT���X6�֜י{_�������j��c���#����;\\m\r�u�@��Ol��K���֛�f��g�g����6����u9�Dd�:�z�M�L�;��r�<���Cݧ�o��A��n�T��7qL����Ӽ[�q�%���\nJ��or\"��8�'�<?��%�6J��E�&�~{�	�,:P�A}VO� ׿�#��,���{r�rp'm̦�2�#��L*s�\$bvc0\"�����\\�)R6�\\�.�޼H��2D{K�l��5k�R�������+=.�uCa����&T�'���#�b��Yv�\$\n�+�U�:���~l;��v��Zri��d�	�\0�WTh�\$�-\"���Hr�5�霓�э~_�\$���.��]��z\n����\\y��9������Zq�4 \rՕ�9�}�##�������������� ��b�\0�Pb�/k\0��ڍ���Vx\r��a|��~u\r��`��N�p��(��t���'G9	 ��\0<�e`��-�Qm��-H��FذL PP��U�]��.n\0�@�ېk��0Ub��<��/-H���pN��t����ɦ��ԋ7l���,F����k!/V��\"e̥p��D��tv�\$�hj�p6L�eFX�#�6h�C�D06BJ	�.bl\$�5��9�j�̔������6�A�f�ͅn=DI�]\$W\r0���\"n��Ɇ��wfXƥ����d(�P�p�]��\0�1�S�Bb�˭���l��}\r���q��hA�T��gpB��NLDg�Xf.�e�<��V��\ra\rc\r�\"HE�q�����	N��Yr�	� K�߂L|&�Pg2�QS��v��\"@�j�����	0�NO�.#F`à�&`�p���ш�U%�=!�����%d&�\r\r�d`p�(ri\$�P�u%��e�Q� R�&�3!��3�a#R�l�^,�'n����p޽,�N1�	��=��,��*2�*m},c�*�F���;���yq�#\$O-B�+ě,�/��'�|NcZN�\nޑ� LD'��6\"�!RF���/���TM1�氨(��=�c�S\0b)A4C5�K�o4+�3P�&e3��+ʼ��%�FL�\$�`��7f+3�w8��B�d�ے	�5��m�9�5�&/Q9���pa��aI�I��k+����h�\0�7�&%����\"�IL8�@\n���Z�)�T\"�vO9M�n-@Q�z��@�Q6`�Aݐ�Ք�G*��p�#\"�0#E���1dOKb/`� \nB�S�l�=��<dN��|gN��� :-�����=C%��\r��Z��=��H��:\$�p�D��F��.��,��Όr���\"�\"&*���J��`k~ߪL-�'��̯^wM�T#M�.3c2���\$����KCh_�nk�Bg��%@�\rt����x�C����Q��=l�9�I����+���S��G\0	��n#�m �-�7������Z0�K��\"b�I6D2k\0�'S2k|��Ms�(RvOB��Uz̢8Gc�n";
            break;
        case"sr":
            $f = "�J4��4P-Ak	@��6�\r��h/`��P�\\33`���h���E����C��\\f�LJⰦ��e_���D�eh��RƂ���hQ�	��jQ����*�1a1�CV�9��%9��P	u6cc�U�P��/�A�B�P�b2��a��s\$_��T���I0�.\"u�Z�H��-�0ՃAcYXZ�5�V\$Q�4�Y�iq���c9m:��M�Q��v2�\r����i;M�S9�� :q�!���:\r<��˵ɫ�x�b���x�>D�q�M��|];ٴRT�R�Ҕ=�q0�!/kV֠�N�)\nS�)��H�3��<��Ӛ�ƨ2E�H�2	��׊�p���p@2�C��9(B#��#��2\r�s�7���8Fr��c�f2-d⚓�E��D��N��+1�������\"��&,�n� kBր����4 �;XM���`�&	�p��I�u2Q�ȧ�sֲ>�k%;+\ry�H�S�I6!�,��,R�ն�ƌ#Lq�NSF�l�\$��d�@�0��\0P���X@��^7V�\rq]W(��Ø�7ثZ�+-���7���X�NH�*Ъ��_>\rR�)Jt@�.-�:�*�d�2�	!?W�35PhLS��N���T# �	Fy8r�!ȡ\0�1�nu�	�Xn1G.�4�-܂0��D�9�`@c�@�2���D4���9�Ax^;�p�`�f3��(��㜓%���\r���	ј��X�px�!�D�3���L]Kjh�{#4T�M\0����\\��QR���Y�r����{38�'�q��6�]}ܢ��9\rАΑ��\"ϼ�`���,�����\"��ֺN�*�\$�E��Z32�Ɓ ���j{W�\n��=&P0��d�;#`�2º�����#ʍO�2n�?�����*������+زu���(&����?o;���Y0���M�C>W�J<�==�M����	�����?���(gbJI�T[����\\�ًkH,0��O4�u����V�\n'���rp�ɓqr����T�������6d<j��|f��t����o¢ ��4c�]M�.� �3�����S��1/`�A��D\$r\rb&��@DD�\")��d�C�C\0)�#rn�S'W!�3�\0Z��[Vt*���py�b�VÃ3��7�t\$�tR(0�p��r��7S�\n�)-e��uN��HC\naH#A���#b.�ո����yR!\"�R�\r�>5�l���m�T7Ȩ���\"�xg�R#D�B����N�Z0s@�+��⚛U-o� 2xk�pE��82�IŃ\"u�x�Ա=�)��&���5�Cd�<\0���c#d���2�\\��4�ٜ�`��=g�	b,e��Z8I\r����|'�>V�>�1�tAO\rm-##	M�k�e�V\$�(E�Y1nS9����AB\n�,�M�pX\\�Y\0�������kSgC5HR=�I�%\$�.b�d�U�Di� tb���z�Y�C�23D����\$�A�u/��-R���dB�\0��������*�]\r�gz,^� ��)0c��B�c�r�`eq�9C�v�����;۩VL�r\r&���ExZ��nvEU,�X6����3��8�\"{��O*CD�5\rge�� R�Q���P)X�l���Iy��4��\nH��?;G=��~�fFA�m�VvY�������sn��NIADY��¥�����\$�KR��\n�p!�,Ec��ݽ�%j���]�5T*[:y�d�z��J`Z�n�QNT��x�\n.:rQ���f������0T�9E��f�X� �A�#��p��z��L'�2N�ܭC����Td�3�	�8P�T��P2%{�٪K��\r٣�J��)Z@�.�њ9�(�Ŋ�\"/L�i��b��ǥ\0�6���T.+�aV�e&�L�ڐ&&\0���?\"05����kĴ�]�\"W��A��v���ɭ\rtY,i[G/)��̿�p���b�Jq}�pD,A�ҡ�T&�֭^8��_��8��0�#�XW����1y�b}�r�#��U��.���*�o�WP��Q9���\"`��	4��M��?ho�N��Z�Ϲ.�Ħ��I��&O�9E�Y���}��n�0����r��0�r핺��]B!�nզ�S�d���C�0����8�C�����A#@�H��r7�Te;���Z�H)����1Gl��D3��Jw�<th�ߍ��'�C\$��;��D�f�ЅI��#B\"\\V�F��u�Sc�2�=�r��V\0E�;�Ķ�O�v萨BH�#�po�����~j��ާ��6�:�x ������d�45p/h��۪C���)0��e3UJ�\"r���Н�?B���O���.��1���/���q�j������?k̽p��nO�\0锈CfІ�{���#n�����,1�6-\"���S\rҎ����kVŰ:\"��0B\"H�p�0zL� �p�@����Ix.�Z�bu	�>���\n0�.(.��@����F@�\$bJ��Ȗ����Mj[�\$P�bP?��,�M��m��bRb�D�WFN\"d*�1E����J��eG�\$�.%�]��Ԍ8��zϲ|\$0l�\"͔1nW��,��}d�_D��Y�8B�L���l�ob���i\n-�D��7=Nf���@�����������N�xjPm�!E��Ι�Ѡ4ѵ���&H{+��\r\0�Ѹۥ�5�\nΐl3����}�W#�֤��B�EdF@�b9 \r�J��R#r5lE��RN�I\"/�([��4���[�f.Fs��)m��~����.�0���la����'h��LG(j�\r� �C ���x�-؞ѝ��}���҉M�*�kO��7�GR��&o�(��-���2Ⱥ�*���*�m,QC�������\"�0:m��3���\$�&[*0��1�\r2\n�0�o(�pU2�#��'��l�\"�-\r�(b35e\\{0���6Q�\r�kr�ί)6U+\nK�����آ�9�&\"��oG\$�?\n��P�% FD�d39�:\$����v'+;Y\$By.%�Sl��]�& ¯<�}+2-���bk��!3��s�7�5����>f�>���Qv@m\"��3��ل���5����)C(S7���TA\"D6��T��D�nܥ�5BG�\0�D�iD�n�n��9.��7���P�W.���j>+�G�l��^���I�mI�� �ȘDoF?����81k0#쌪�>��KQ6�/f~����KBH�S#M�y/�N�B+��E��N��&���͊}ht��B��m��ӎ=�P��y�'\"1Hx�u�R?.�,����\"A~�(�G\r򵋏Up��%E�^)��H4OP/�H�J4a#]W�F'�Y��S���f�Q5QX�����t���/<��4O[B\$�oC��շ\\T�IqIS(>\"�\\�\0\$�[S�'B��\\5�8o9Htj���u�U�-tND����7�NJ�D5�+5�\$�J�s�1��M��F�)����/��mT	����j��J�n-	k�:�U3G)e�+�=�dKEBMo�J�m6'��v�g^R��.�3�\0O�b�@��3Q3*��6�f�֮4��i4\r�V��=�PLv.��*�:t�(@�ˠ����E��\n���pWi���'sIDvr�Fʼ֡p�'fJ�v��3J<�PҶ����JK���eJJS�\0	�޿��/,%��C���!�a\r�QC�K����Jp,q#�%5�1A˪&*���sx1�@��1	wCH{O,e�T�n>Ava�3�ma��{Q�'P&��ܙb�4�u|��&R���	w����(UZ���~��~ru����w�w�@�V��8�3YL���v��t4�\$��Q�xQ�~�j�7��������%��փ>�Ї+ҫNu�`\n���\r�A48	t���ÎL��l�1/{�G\$S G�w�w�\n%�\"s��-'ҿ��zD�4���_����.�5�2_�:.`";
            break;
        case"ta":
            $f = "�W* �i��F�\\Hd_�����+�BQp�� 9���t\\U�����@�W��(<�\\��@1	|�@(:�\r��	�S.WA��ht�]�R&����\\�����I`�D�J�\$��:��TϠX��`�*���rj1k�,�Յz@%9���5|�Ud�ߠj䦸��C��f4����~�L��g�����p:E5�e&���@.�����qu����W[��\"�+@�m��\0��,-��һ[�׋&��a;D�x��r4��&�)��s<�!���:\r?����8\nRl�������[zR.�<���\n��8N\"��0���AN�*�Åq`��	�&�B��%0dB���Bʳ�(B�ֶnK��*���9Q�āB��4��:�����Nr\$��Ţ��)2��0�\n*��[�;��\0�9Cx����0�o�7���:\$\n�5O��9��P��EȊ����R����Zĩ�\0�Bnz��A����J<>�p�4��r��K)T��B�|%(D��FF��\r,t�]T�jr�����D���:=KW-D4:\0��ȩ]_�4�b��-�,�W�B�G \r�z��6�O&�r̤ʲp���Պ�I��G��=��:2��F6Jr�Z�{<���CM,�s|�8�7��-��B#��=���5L�v8�S�<2�-ERTN6��iJ��͂\n��\nq?bb��9��m���Ţ�L��\r�\ns;�9hyz�Z��I���+�&aX�JRR�Bٳ����ۙ��Et��It��&E���[j��ndF��ĩ@ ��l�3����O��>�1�����p�8<C��������O��2�\0yӍ��3��:����x�߅�/7L�t�3��P_?t�L\0|6�O�3MCk��x�P�F׷0�S`T�n���z��1\"�pP�R���U�q~�}^�TC�}��.�RN��|�!i@bt���~0I����R��@�4�/��WS\rA��J#�p���W\n�9��%��}���,`&���ᛁ������u:!BB!���p��9�+�>�6��r'��0�P؞�a\r��2�󄽟J�)J5�`��te�����DW2�B`� �p��;�T�3ô��sH\\m��j��g��fGe�u��Gi0	5�dIZ�e\\�(I�	I�N,���S�\"ލ��6FHa\r�(�'��:[��3�#%r��DB\"��xG�(�I�B� ��5��\\�ح��]�2Mt�6��Q��VJ�N�T���\\��gĢ%�y\\�6tC��)gq]��`.d\r,d���������<ݔ�a���\$�6�3�(\"Y���I�O�*MxG�J�0M��4C�i՝��x��=�T7��i7���Js�uM�1CX7�&�0	���rGl�����0f\r�,��~�<\n����AorN]̆h�\ngI�ֱw+ R@ �0��S�\n�)+�)� �R\r���s�u+�4Z((@��d���HsV�ʷ�!��7Y�b(�gWjt��`Gy���E^Ze�.Q�i#\n���\$�C��O��U��p@�gu��R�0��y�vN��0D��\"�I%��S��T-J�iY%��!)�MV��b[�e����3�c�c��6���@S���o1�:���ivJ\r�;��]��wn�߇w��\\Zkx�\$7<�R��+�zT��fT.�>c����Hf|�`a\roY>�z͉����(�Ɣ�A3`*N�ز�Y�\r/��������r��R�E-\0b<A�3�&+�f�I�<��-k�ytG��������p������eH�P�C���H��O&y�1��\0�1�l�n]0�(Y�L�\\���hs����\nj��G^|O)�='���Xf�Ðt>�;:ihþ�XQ���S��{R�(�E+�x����0FG��?kNGCL\"O��k��lh5[Q\$3'f��r#&��]&��0�tn��K��J!>��f���Ȇ�_|WLm;U��s�;����I#���8��܁ eX��dc#@�4�ۏ�%�?,�9�4�u�~=�m��6��͊��\0�¢��a���4fm�7��pO썥\$�6�6F�o���l�>~������0i��9H\$�0Pe�ǇC�;U���S��2��Q\\�b�@�)��7��t�*i	u�M\"�5�<q�)ă�O��*�}*t�T�}�I��r�0O	��*�\0�B�E^h@�/�n��!�'ЃΥ�F��������kW��(���3�L���J���JM���=d���)�&��2���Y���M�E�n����0���\n�w�~���)�_�3X*B�)I&J�6r���C {��5�x�\r|���!�§*\\�����4��0�O��͢E���d�\0��/�m���\$<|�⩍�}���08G���UxR���������I����~�)�-�\$��F P\n�X���=� ND&�%0�,�i����\"6>��-�1�LV?\$���\r\0@�� ��TTl�0�KkĽ�z��\r�&HHL1�\nGfv@�\r��x&�.\"+�K�%��\r �F4�����=����WO���F�p��\$t�j����~X	�*��\0�\r�«hf\n`��Ǵ�K�>�����f=@��K��4+�Ʌ\\���QD}j�P�k��/��ј�T\rѢ�z�f�a�1Ħ��JN̷q��o����:����K�����N��~�b,�G+��	�Z���f���`�q���:3ʝ1F��r+�,k��%��~ǲ>ƆR�,��T�dm!p� �}f�ќ�7���Fj����e⮑�d8�@\n��`�1���jO ���Pr(SpRA��Q�^1�%/t9�6�j+�cq��f煪��&p`F�S���~�GO��	��a��/�Z����k���(�p,~K(�-�102��Ol�/hAB\"ݳ,fK�/����^��\"��!)�/-��\"s2pR��0)	!����r��I�H�+�.�+�'�&��.)a2�����3\r�6-�/�+,�-Ӎҷ2R��9��S�3s�2��\\�p��䲀	�t`@M �V�G�+�m�z�(ϩ>��C\n4��+v��?#FGJZȰ'30+<L�GH�y5����!�\"�HJn�\"�^FB׊���۳PR�@T^�K8*e�;~���A�)\nJ��cF���'Gb�p0l�3r�o�QCG�Bh�7�DRg�0��Cf꠆=�XhcZS��7��8joϹ���%�n0��)2�w3�ತ�'�5Hs;���NS�Ik�8\ngPhR�����5	N�G�2.������<4�<e���.��\rSs����:U'S�t�\n^��|��8���U<��AU5,�/h��!B\0�<.�n}�xW�/@��L�P�`̋��쭟@f�@�bc\$�<#R8,�A.�+ջ0Tr�H�g\\��ǥs<���5�E��-�a�ADc^�71�	���<95S��_	P�Ȗ'�e\0@\n��ː���[,��'��'j|��W�Y&���cu1N5iB�YB6\n���s�CQ�6=EK4�`R�e5d[dP=f+P�T�RU�W/�9VX���f�30�9V֑�V�q9J\n��P���g�WV��J,��<�t�Dv�k��T�	lh#l�;A��Jt�`6�g6�eV�^�+�Me2Rдp�h#Ig^��od1C/u�3��\0003D�1�f�H���6�·���OiV�����[q5Q,�wj�!rT?-Sy5�����)si�C}u�\rD�����vVKm�MoV�S�i%�:�T�;l#UW�vכ1��)�VWSh�WgmTn��tI��C]a	��0��c[ir� �I_�QZM�T�H+Wx�UgN_	2 Q@�L�Tsf���� p�M��A��2�9K��x��~&�|N_|��}7��b\"߬�\"%(51|��b�HR�&�]5���f�	)Y�@6 ��ЗIX\"��&�0����w�ם5s<o]Tm��%0yn���xY�]}rU�1�AHA/�j�V��W�z�)c7��R�����s%wV2�h��T8�U��=d�'����E�	b#%�&&���\r��������h��yIS��]X�z���(�񹋈{��z�%�۔u��y+�����cBy7{�%%����f��~yO��S`����Y%\"�mm7я�HRO��� v1\\��Ђ�%&��xn8oD�X�W��ӝi*ZC�K�Q�3j�t8����7����|ApY�i�/j7�l.���q�����6�S�܎�L��?�;98in����-�I�Z*�̫�I�ұ��%\"ak�ĳ��9��V�D�2�J�Cꇦ��7��Un�:y}G���y�d���{v7�.�?DݖQ�]^�e���3�:��z� �e���ya`P56%x�5o:j�=aG˭�\"�������@�oe%N����v���:�9mW�Yq�I�.������8ߨ� Q�x{+�:\r��i#�����+�����#���K�I\r�5�[A�{7�T/��͵ZS��V^��cy}^ف�pwT�rF�W%�E�-�]��:�;�%[��歺n���fe�Z�]\r�1#�cRӡ�ѯ���up	����p��WF�4¥��\r��M��\r�9#��D��:�]������e�瓷=��-u�rs^��|�|��#/'w��^���K�K�Y�B���(q���Ò��ԉ��K8v����nّ�\"\$ڇ�s�:�QO��&\\3����Y�/ �e�\r�Vj�`��t�Z��Pŀ��\0̭F+���\0�\n���J�L��\n���pd+���8S?�[>�\n7(8H���Y���M.[�P���{St�!ϵ�����H�s���\r����8�j����ςy�}]	��8�4 ��Iƭ@n��� ��t�B�_F!@��ЬAD,J\"�&�8A�?�b�qog�<�W#L�v��;\"\naF}��pU���q��=���ds���B���ډV����Ky��:�*���}��ϑ�w6�ӫ8J)ٽ���/zYޗ2��5ޣok���5��y[i=��5��p�>iB���ꛩ+��=�9�9�[��{_W\0�dc�;��� ���5�V�m��vq�\$�(��gGVY~��>e�������.[�Xye�LI'����t����[��ꨴM�@�d����}h[Ј`����8��J\"~	�\\qQbf�\\Jǣ@S�aݰ�-�}C�=Ǒ�dWB��'��~\n����lh}��a�6C���+��O��/���D�M��?A�jw����z>��ً��f�v�A���D�E�DN	\0�@�	�t\n`�";
            break;
        case"th":
            $f = "�\\! �M��@�0tD\0�� \nX:&\0��*�\n8�\0�	E�30�/\0ZB�(^\0�A�K�2\0���&��b�8�KG�n����	I�?J\\�)��b�.��)�\\�S��\"��s\0C�WJ��_6\\+eV�6r�Jé5k���]�8��@%9��9��4��fv2� #!��j6�5��:�i\\�(�zʳy�W e�j�\0MLrS��{q\0�ק�|\\Iq	�n�[�R�|��馛��7;Z��4	=j����.����Y7�D�	�� 7����i6L�S�������0��x�4\r/��0�O�ڶ�p��\0@�-�p�BP�,�JQpXD1���jCb�2�α;�󤅗\$3��\$\r�6��мJ���+��.�6��Q󄟨1���`P���#pά����P.�JV�!��\0�0@P�7\ro��7(�9\r㒰\"@�`�9�� ��>x�p�8���9����i�؃+��¿�)ä�6MJԟ�1lY\$�O*U�@���,�����8n�x\\5�T(�6/\n5��8����BN�H\\I1rl�H��Ô�Y;r�|��ՌIM�&��3I �h��_�Q�B1��,�nm1,��;�,�d��E�;��&i�d��(UZ�b����!N��P����|N3h݌��F89cc(��Ø�7�0{�R�I�F���\$!-_H�[�����+�q���\r���sЅf�L�X\\5��_��6�bw��v���;���M� �ֈg���n��l+�ɛ�N�*��� ��l�7������A�S��1�o�U+:�S��;�0;��>t=9�`@rC@�2���D4���9�Ax^;��pþ���3��(��ÝE�\r���*�ӈ�ecpx�!�}������W��;u��2*�\n��Y�h���̳c1�M���!qLS�?�~�2v�s�8,�ӣ����9�Y'n.Ap�ΰ\n\n�9�Ù!���!�\\�!�K(p��A�K���f\$�sѹka������jN6ϕ�,�����'hp��F,�u\r��;�C+K&!���O	�X�	\\��T�'�`P��lJ�_+|�\"c�F�����쬇�\r�Blȴ������ʥ+&>�9\n��.��d��0V�IqB+T�������]S�vIP��a�d\n\0001���Qj���0�ڲ*�ex��*���.����WK\nLoue=�_/��v��,�yH+�+*���3�aJA�h<�S\$D�.oqY�ɦJ%����	I�ܘ�Z�}�*�s�5�〰:��8���E��\")�7f�/�ohD���Aꂇ�7�`����)�(�\0͐ѹ\n���<��A\0ue!��8���\0l\r�*�7D�(!�0�PAO�,o��:�PP�Kב,>�[�\0C\naH#Aê\\j/;f�@�S<��keA0��[��*�sѧ���Dm�9օ��0Y��Cؚ��d��ǆp@�*ntA��2���!�t��0D���]�R��s�U5=��z�H%{6\"�T�%NV�o'�ű���?\r��E���tv�J�MN��<��hx�Ө�F�l��u���;'hݳ�\rɸ;�z��3(eL��p�C��\r��:Y�|��[��g�<����*n�ױgח�sV�*/���.�[hYe}�D1ٶX�l�!����`�\\!��(\nX�^/�4ʚ&�0���a���\0�so`i����[�s��`U��jN��c!�h�J�W��:N�^\n (\0PbJ�B�%X�w����`���U�PCe�o�s�|���?�2���	O�+	��P}b{Ŏ��*�����<�T�,v�f)�V����XT�Y*���]�����杹7k`M�R)u�i\$.-�+\$�����CLp��_4]8q��?�d�l=�p�b�d�1NrP��<����r�p��wτ[J�]'�WM���i��!J���,��������Y!BQ�.{l���l�܉�\n�U�i:�V�J�*����J�\r2v�D �a�pL�5���0Tɲ�\r��С)�����9��*5EzX�ͱ�\\#�i�1�(�@PO	��*�\0�B�E�<�\"P�z(hu6�jxF��7Jh���匭:�i�E�Vȕ1��T�]i9v�X�.!|4��&Ӷ�b�d9SJ5��ר�w���CQ~;me�3Ma�K����Wv~|��?֝��T���b������H5+�V�q@���s�j�Y���i�+�����������/d�jI����K��[�\\5Wr֜d��b'��C���nR���Gi�H��b��!�5m1��C�Z�iQG1^��m��������7&�V�grbջ��k���d�փc���^	d��Io�%�.b`����eF�D<'�C��+�&8�v,c</(��(�ʞ��r2'����ԥf&h�TV\\M����+\$(S�|�#��gBh?������DS���c���;�������E�8�B�d^�\"�a�v8\"��p ���&��}�lB,�Nj��\0� �	\0@�D�\r\$��l���P���\r�6����{��X�� @�1DJI�[&�'e�\\D&q��F��VU#�0�6�m	܅\"����ϭL��@�P����1�mKlE�:��&+,�Bߣ���X��5���E�[f�8DF�k����2��2;�9��\"�9C( ��H��q�c&XqT����QО�W#A�:��vg���\$��OJp(cO���pid%q�XhX���m!e�[�v�b�[�6[�~G#����6b\0�m#�x9�?�;��%&it�P�D��dP��ة�f�b��I�V~�v�VA�x�G�Q��e���~�%�D����S��D�*!NA�n��8CQ(�6|'	���|���7\r��r�|��:�I,�\"�kt����0:����\$jɄX���#Ј'o]@�N\0�W�~+2L��B�:��F+g�S-�2��9r�#R֒3.�m3e�1C[nR:Ix8�}D#03g-��Q��/^�09NPE��E�L�μ�o�\"�R�Dp�\r\nmO\n���DE(��/E��,BF�5>��r�.\r��<s�\r�`�!3�.��i��3\n���,sy+��1�;�<�z\no��ILz�4\r@��s�~��6�0~��>sz��S)���V0­�+RT����0QL%�P����)/L|���A�h�I�Jag�+�yEB�鴀8Ԅ~�Q�g��?45A�C�mG�QG:�}I�?㊍�~͢�e\"�\n�>�P��8\"g8�fC�L�\n�<%�LD%���������Ƒ.�<Q Vh�r���s����g�L<\$Y	�R}�DHHTb�M�M%KJ�I.YKsnQ-��5tڏ�&��E�ݭMS'�Se3��;��J�u>��6��G�F��;S�/Iu�.4H�fH�gWg��c��\rP�(r�Xh_�\"��՛B�JG�W�Z�Z��[U�=2�Cu�>�>����[�����[P�Y�_���ƄV�g1��:#h�im[ja�2���A��1�aaљ`�`<%Z@�sf�Bq�H��d�L�vQ;���]5�ZJ��O^4Ru�f4�fu���@���̦Y6T/?n>d�q���O`5'hjUh�OY֦>��i֠�2�\$�[�)N�'��1�	c�7+A֊=��[��m��m�_K��g����q��D��Bӿf��ov�n�p�i5�p��JV�n��gT�,�%��[��n/3ev�Mvԉgt�C�VO-r�^�lV�=wV��7�tX���\n��	6�X��wNw��p5�hmq�L��vp)o7K^�;g�p�4�	�u��zח	Q�~��_��(\" ��kD1�0��zWh0�m7�˷=^Tv��}T[}�V	�\r�\n�M|�N���.�+*��Ȭ��b�z�V>�2�B�p_\n�~�0gD�h4��'P괐xи4a��2��\rH�n�4s\"��Fʢ�\0�`ƣ\0�\rm�\$��/��&�|��+\0��ڰ��~M��\n���pc��/QR8�iq#�+�[:�Y}�d-N��Ÿ�mN��TL�\"�	�ߋ\0�.P��fVp9�� 	�a����]��	u1�e-8��㝈*�J�~�P)pU�0v�B*+\0�\r�y'NrdY6v�/\"%2}ojMяV���k�_&TU3����t̜��@�ʩ.�UU�TLT8б�6�q.�l</A0�<�k�R���s��@�d�=����ؠ�6o�\n���]t�fթ��a杌d�+����7��9�Q��\r��8I.��y��y���E�;��QY�%�C/���4��X�\"�\n���\r���9!�:�,|[�zՉ�2�f���h;�8;���1��gl�⍖<<��H\\J�C��<���b�:.SA\"�<68An�|��Хv+��_�́�\\;�Sl䔇�)`	\0�@�	�t\n`�";
            break;
        case"tr":
            $f = "E6�M�	�i=�BQp�� 9������ 3����!��i6`'�y�\\\nb,P!�= 2�̑H���o<�N�X�bn���)̅'��b��)��:GX���@\nFC1��l7ASv*|%4��F`(�a1\r�	!���^�2Q�|%�O3���v��K��s��fSd��kXjya��t5��XlF�:�ډi��x���\\�F�a6�3���]7��F	�Ӻ��AE=�� 4�\\�K�K:�L&�QT�k7��8��KH0�F��fe9�<8S���p��NÙ�J2\$�(@:�N��\r�\n�����l4��0@5�0J���	�/�����㢐��S��B��:/�B��l-�P�45�\n6�iA`Ѝ�H �`P�2��`��H�Ƶ�J�\r҂���p�<C�r��i8�'C�{�9ãk�:�ê��B��} P�\r�H+%�����4 4��Jb�J�=#\"7#ʈ��>C{��?�\n0�l��\r�8@���S���H�4\r�.�����2�\0x����3��:����x�c��\r�#�rJ3��_X?��^(��ڒ��̒ǃ�����x�\$��>���,�#�|��,�m4#�2492+�ڼ6ʝO�N���'�����}	�E�R*��\\鄣\"l��N3��-H�<�+t[w����'��K�4�\r4��pT�zB�	?|�wiN�փ\$��h%�̢D�fC43E8�.��:�+f� ���1�-H�ϥ�p�����F��Թc�i�(�����C\r�5��M���м/�`xi�O\$X��B\0WƄ���������ꔥm��s�5�H�|��JW���-�:iu���� ��q�����d�d:����'^O��.=\$�J|5��AÄ0�=A<�v9eU76Ԃ��.���!v=��&`��n����[��I�4D�#���A�(aL)`RQ�8!���i�����N���=n�����Z1'ĝF��\"�C�*d�陓6K�\ra���:G��\n�]�Ҋ���bĲ��������~��ܹW<|o�9���qɘl��Z���2C���H��C	\r*��C2�8�.��t�#c��6'2��[�Cj\$�!DEx���X�d,�\\I�z�\r�E.�&��s��8ƛb�>Bi��E�BnH��`F���[	��\$ͨO�ej`�9�\r��50�Ɂ�ƺ��b,a1Rҽ�\nw���d|D]��N�I�#�ɺ� Iª\r�z�R\$|#Y!R(��r'��E���0PQ`{P3mMu�TQUHa��9p,K�C�R�3�����ME.B	c�\$��f|G�B\r3\r� ��2CI't����\$��^��kQ�%FYf��23ƒ��Q�hIN�4I�Q��EPB՘�<�^�p�JS�{N1�D�����\r�U��R�K��\n<)�CZG�2��A����Z�iQ\"��F��F��%T:��Λ�8�9¡cS�\0T��6X���}qA�\0)��\$R��p�\$�Lf�oϪm)�4��\\q�p:��'�UW�)���\0�\0�A,0�� ��\0U\n �@�@H9��5�sLa\0D�\nq[��\n@Uϼ!&[�v.��t]�\$��8�2ކF\\��n�ݹ��t�߁�I�Ά��Iԁ�:f(� �P���!�㢰�rR��ns_��f��4t��(V��{�b��\$9Lq��s��3�6���`�f^`@�W����O����B\$�!g�>��xA�WV�\nz�|3:�q�&NȠ�'2�p}�)�r�^�\n�7-�\\֜�n+f'&��\$�����7�IRh\rJ]<\$�4����Bp ��*��&-��n_�Ը��}Wf%:�9Ϸ���J�:������A��>���\0000�nl�K].#1�d\$�^�r��W�D'a��i�,��qqBz	�.h��Q�����gN�@��A�V?�Yc<I�\$�aMg�T�KNc༫��\0��YF}�o���X�\$^o�����e�[c[���{�g�19��vn2y3f|�B����J'5�:��a��gμ\"�)�&�م.��?�-�ft%����q��\nߞߜ���3���A�<���s�s*uC�j�!�jFH�?\"Hj����-+�\r��)��ݻ���1�Sϭ�\"΋�����}NA#x���RyQȇ0��=�c7*�_�\n	/�QEX�ls��\"뉛�4Fb��2�K_`薎O(Ӎ���p�u;\$w�d(����.x	:��Z晳g5%3�@��V��^�h͕��B#_>a�����w����S����R4&��:d,*�xOȴ��I?�&�L:ޅ�\\&;ƞsc��'�h�B�%���ŧ<�Ͷ;)\0�\0���&}Z�P&�K:w�0/f��(�:m�Vcp�nx*f,~�Nb#\n����`�7.N@��`�N�f��j��6�Ѐփ�����\r(�	0���B�N�K&7�лb�\0b�F'ńX��\"#�\"G�:V���F����t�l;\$�\r�7�ЦA�X��C�)��GZբm�<�L�a��8)�9M.�/���c�҈��0*�\"Ұ���2��>ȍ�0P���>�P��N1J|���-�(q(a�_#��(�㸻H\0Gdz-�x�cp\"�`|�N�m�6����q0\n�06\$�;�*\\d��#I��1�f�:1\nPID�D�]`�B�����ì%qR�D��1������\n��8��d��/h���o)�y\"��\r}!�^�q[#r3!�#\0Җqn��z��Q�;�^��,��\n�X�K/K&abN��n���B�&��@]R�4+�-�t!f4`l����N~~.��2mbD8R�N����\"��e���3Ң��߮��f�2��p{OH����d 2�l3�6^R\0@d�\r�V���b*��j\n���Z��l+:�Ni-K��ַ	��N��R��S8��1l^�z&\n�\"R�t�|H\"��#�\"���^O�k/I&S^6f6*d,���(ha%��~���\"�X�C�+V�Q���{ǆd�\"7(P����,��xһ<\0001�����1��a+�vͯu�c>h�C���Q\"B#�&��Ӱ�7�O�_3���X��	�,\$��/���>#��BCd�a>m&̉������8�TO@�� \"�\0�5'�#@�_���E �&7�E�\r�`x�w6s�O�����z�-�@ds�ORL��\r`�d��\nmL*i�\"@";
            break;
        case"uk":
            $f = "�I4�ɠ�h-`��&�K�BQp�� 9��	�r�h-��-}[��Z����H`R������db��rb�h�d��Z��G��H�����\r�Ms6@Se+ȃE6�J�Td�Jsh\$g�\$�G��f�j>���C��f4����j��SdR�B�\rh��SE�6\rV�G!TI��V�����{Z�L����ʔi%Q�B���vUXh���Z<,�΢A��e�����v4��s)�@t�NC	Ӑt4z�C	��kK�4\\L+U0\\F�>�kC�5�A��2@�\$M��4�TA��J\\G�OR����	�.�%\nK���B��4��;\\��\r�'��T��SX5���5�C�����7�I�����{���0��8HC���Y\"Ֆ�:�F\n*X�#.h2�B�ِ)�7)�䦩��Q\$��D&j��,Úֶ�Kz��%˻J���A�Q\$�B22;`ՠс� ��N��R�4J2l��2R�?\n7���TE/d���&�\$��A+��\"<O+�>��p7W�B�`�V\0�<;�p�4��r�P��� ����7*��Ҙ�4}@��d*5jU�]\\�T�8�Ҍ��욲�(�b4H��J��w1�Q����^��x�)�a��dҺ�P�2\r�rH�;�%rd�#����I�H���K��IC�(�c��0����9�0z\r��8a�^��h\\0��{�x�7��)J�xD���lx�D4x6�����|��*�c�\r�b��JA/�	N�M5�\0ը¡��pj��`��Y�m�w�Sn ش	#f�⭫�����cG( P�0�Ct@3�!(Ȃ4^�݂s�v�|�W\r�ev)�\"@�������j�%��s���(�y{�f�N]��_3����z�:��P�0�����[��5~!�Ѯ��5�5U!~�����!nh!,��Z݅��R+�)ShaɱB!���:�M�&I��*w>�JH�3�8�884��A�î�H�c!����I[�DhY)�xbQ:�%'��C���LAvĭ;V��q�͈&�\nGa8�&�3EG\0004b���1��B'v�|�#@O�\nIc�n�ur�c�����g��[]�)� �4�Ƌ�v.�3F��� B\\�͢�d����O�9\0��p�A�\r�3�4��\n��嶀�A\0uY�Փ2���\0l\r�4���!�0�\0A1��\r�����\nKY�7ja6�G@S\nA����!��u<b�a`]�p��BP\$L�\n<�\"W���L�&����a%E�<\n��f̲C�)���̤r�C�Yᑍ�ӈ�_`�\$U�[lm�M�3s#�2\$�.�QtKF�.��M�lԚs���)����T)�d���xR�Y-i�3�p�ih	TQf~�ZEh�%�����ڈnG ��5f���j�Z-y����mj�ґ�\\���;\0���ȓ�̮�t���.~�QS!������\\4)�P �r- Z ����\\�@pG4�:���RK��n[��u/�1<^ˑ��\\�\0c�u�4���fc*to�x٪�F���))�[�\0��-?�\r<\\W���Ag)�w��o�˂(v8G/TD}0{'�I��\\I�CZ5�1�U^�i�:'L2������<I!�������!��L�u�CıKn�N�s�S�Z�1	��9\nÈ!D����,Cc1�r% ީ9�ݰ�Z�4B؅���-)b��ԼJ��CStD�ПI�I\"���1�����j���f�_PsIA���XX��j��]\$;i�ҁ�=\r�D&��B���`�����	�L*@y!q�G���b��ʡ3�{!=\0004-�F5[/�ݮ�\np��ˬ �ɹ%@����s��a�7;R�2��hXS���3_�@t�F\n�A��^k{#fZ�.� �q��\rs���X� Q�*�o1�!RVA��\nѰha0AI>��pC0��A��Dh	�����0�A�B�٣A��z)]\$���E�x��P�\nv����]��M�A}�� ��:��%G���*A�}}�=E�H�!�r3	E����i͌Ơ�5>\n����X����>�\\M�&G�a\"u�5�cF�\\��G��?:K�\"��zi�8gJ@{���=>��=sn}Ѻi��[��g}�Ⴆ��к���h\"ڊl���?n�k����ڌ�r�!���]��1��0�t����|� �K�Y��0��aQ��9����:�C�#r�G�L\\ɢ�qІ��4]V�Gr�y	E?}�׊�Aj��u�F�6C(�e�(�5۩)�s�g=�\"L�ƍ�At�������/�*����4�[��}�:x#E�.�B^A��!Ϛ��^�!D�p���v(�\\l �n��f�L����^DV �\n��`���D��,I`��J��[��\$��LJP�B \"g^��v�r���Gr7x2�q�vn�Tw�=(r��p��BCJpڂ�O\n��*��h8\$�tppxo`\\0�tp���V����\$���C(�.0�g\$Q*�Z�	��͂O\r�٢?\n��{�u����|�I*�����w�\$\$��)���xv��%P�\r0�CP���I�Lp�\\��*=�}'uh��4xq9���	�P5���\r�G`�#0���&6\$��`�FA��%�\0o�E��\n�@��h��j=	�]\"]%b-��m<�e�E����)�T*Q�c�5e��ΨL�m�\"񭸛Ș�.w�VZ��*�CB44g�b��a<�j���44�@���#�.j41 Lnu,�	d�#��tQ\$�8t�X�c\$��B��I�A%�\$Bb��?&�p�������h8��n�ot�bK��h�)lߣn��rspjW\"�ІA�v2H8Ahp�<Ce//��	 G`�O�\$ۢ��nJ�\"��K'�QIĲ2�^�E/�\"q0�,g��h�)/�����ry�)Q@��.s��9DT!�4i�`�J�\0R�s%� (���#	�G2��5��\\�^Z�67�4��+6SV�sZ�h�5�*��(%�\\�5Ңѐ��sd�OJ��o'&�':n�(��8��%�;H�;��%sdn-�\$��6���Ž�L��U\0� ����2L1ERO9�%9�ܨ���s܌���&��>�O?�,�_?��6�'A�)#A،�1�p���\$£��)4-@M�!m�\n�� �*�#:�I'�/�����f�B�G#�Gm�F/��3�!��G.�3��%�s�\0��r�*��%rKC� �j`+�~�\nCl\"B�>�\$&0�.����f�C�1��s�L��_� �R��4�2H�����d�M��.�\n�\"�t�FSFG\"�/��C޲�(���H��ֹG�*s:A�@�G\"���<�X���p�F�sG�=]US;ҫ:)�����jrq��wE)/65W���5S2��N�Xs�t�;�eS��t�Y';I��W��Y��T��E�)I_A�]�@1b����PuN��W\$�,�N{N�!�#]�\rD0+�`䒁��A��p�5rSB��X�_��a3�ZUv�ӑ1��~(��;ZϪQ��b0չ�!cn'c����L�2�a�d�, 븝�!M���DP�8���4n@Pk�`5�b�N.����.�L���TT�<�S��H֟YtWb�@���j��EM�i�9[��P�i� ��j��VSdx��V��W5Wk�	K�e��K��D�>��m�<D�E]kVb�p/�+ԗb��`�0�v�q�l�P?�U��6TՃY-�]P.�HT��set�%YuU�a�b�\\��vV�W-r�JA��N�5`�AfnS�\0_)���vB_v9шUǽ4!6(4C6B��^��wVF��C�?��^��_��z��x��xB�Q���zM5/]��gk�N6dbW�btUqqe9{wπ�k��/%��@�e%U�)TK#�p�)�\r�V��ml�>bS1�L�p=Xkє���l\r�IK@��n\n���Z�� �uq+��4n�V�uJh�EQQЁ΁�e0#����{@P��1\r����z�Ui�-\"d�(�	��2(o� 	�߇��\$F7BS!C�D�~�G�_�CV`���K� ���)	>ʉU��x	��զ�hX<���us8��x�\0��Dk\n�#ꉬVa��Qx��T߁c 2Cf��\r�r���	7,bTs'�J4�Uo�x�q�i�Ӛ��op�!(R���xQ�@?t�����+)�s��(�UN�4T)�OT���3�!�@���.������0��Ns4be2 ,��*3�\"��%2\n�J��\r��K�7�L���zP���E��C��`QS�Aq�S�#�+S�18�\n��3]x0�eÞf��������Q�iV�E�>w��N�҂�]L,�wDo\0";
            break;
        case"vi":
            $f = "Bp��&������ *�(J.��0Q,��Z���)v��@Tf�\n�pj�p�*�V���C`�]��rY<�#\$b\$L2��@%9���I�����Γ���4˅����d3\rF�q��t9N1�Q�E3ڡ�h�j[�J;���o��\n�(�Ub��da���I¾Ri��D�\0\0�A)�X�8@q:�g!�C�_#y�̸�6:����ڋ�.���K;�.���}F��ͼS0��6�������\\��v����N5��n5���x!��r7���C	��1#�����(�͍�&:����;�#\"\\!�%:8!K�H�+�ڜ0R�7���wC(\$F]���]�+��0��Ҏ9�jjP��e�Fd��c@��J*�#�ӊX�\n\npE�ɚ44�K\n�d����@3��&�!\0��3Z���0�9ʤ�H�Ln1\r��?!\0�7?�wBTX�<�8�4���0�(�T4BB��-Kd�P�ɒpS��Z�&��;�q�&�%l��%Kr!��\n&�F/c,6J;rb!�åh��,��Vej�E�-@]��8�LB�6�o�	AP�AÔ0�c\rI������;�(��:��\"9�p�X��9�0z\r��8a�^���\\0�w+����}�x(�2��\r�������҃px�!�\\,���˳4튂h	K)Ft�� @���a�V\r�K��-��B��9\r��Ί�\"�<�!@�� ��N�Đ��I�`�0֪��J��h��lp6AC��6�(�1BT��Jv7oL2pJ���Gg����5�%���V�]�3ɆQ7,tW�ëg�	}��6�C��(,� P\$�������L�Ѭ�(��S;��F�B��q�bR��\"��&C�z\r��436�J���\"|�?�<�� �g�*�@�y�Ͷ���GKK��\0��j�٬����s³IB�J�Ť������&Ķ5z�с�2�\0����S�\r��w�PY_����0��2�\r������([y� i�a�R&I t(�����iފ�K/\0��Z�ȸ�L�t�E���e`M��*IrlHN��3�Z\n�\nA'����S2YZ�~3�\$� ��#B�:�君W��AA�x��Zf*4:�B6��\$CL1�P�3	-�����dEЍ3vrK��h\r*��\n�v�i;H		����yoFnHAB�ՠ�V�=��U1�\nw���0�Ø�b�Y�1�9(O�.d�72% ���)ef�aBL��m������\n�8���`4!t[�\$/SM0FkP���/����M���كfR*5u����\r��4E�GI�F�x���P���R�Ш�:.��nX��A�3��[����4t&h�	xN2�%!����v�8 �v�g��	�t��a�>t|��9��lP���Fe���-@*�,蘐\\D�HA�V��F���9w��7&cD�b-E\0���E�I�?���\"H�\$�\$� ��\$��76b;[�a�@�(3��(�1h��U\$�kQ��k̒<Q	`/\n<)�H�w��9��\"@O���۹3#�4��Q��'a�e�Eڡ3��\\鞎�Y����2O�H�5� ���`�TN|̐g>\0�e|rV\r�X�5\$8��v�q�嵬��b�%���\ni6�,I\$��U\$�\n�H@���LK!��a�X�!\n�]�֪��I�8K=\$���A	V,-\n���b>A�9-dl2^D�wqT	�����5�@�`��.G��~o�9�����sSy_�f�|L����Е\"/��̻Ő���\n*}QQ�D7)4Q��X\n\n����b8����(�M����Sx�³��\"^Lo5&�䆹�Z�[�8��.#�xP⫙��/h�O��X�굝���[Q6w���w�G�MB8�0׹w�Qy�Lx䪀�����~#a�φ�px�u��h��-�M��]�j	�	]L�L�����(�%H�I��D�M�Y���N�*@��DX�+��5؃�!�&��*]�����5�@C�9�2I��-�bN�̒U���g��vJ��F�dF�9h��1Y?I��򭡂]�7)|�a�Ȼ�&d��V���\r+���E�\08��\$�T��\\���8�����pm?j��kE/z��ʠ�xh����=~�'�p*˱O�%�\$�������+σ�+u�*i���X*^��m̲8H&�DJ1\"���@�ڹ~�������]����������\n\r�w�6�]�@V�������e]������TɄ�������~�˫h���b[?�� ���7�:ń8�IV�F�Z/�Fa�/@�/�M���o��6���Ͱ'\r�C\\���ǈm������Į����\$���.Ō��d�*�D�G��h���~d	�h̨�fg�`�(d��}������\\�dI�jT�(?\0\\�#�l\"�lΎ��������g���!\"�C�5�\\\"м�\$泐\r0 ��\0��6<�\"'lt�H\rP��\0 �di�N`�X��,�C�����\0�g똅�j:bd�q�\"�*��\r�ʋM�/�6GT����}c�ŤJ�&�j����\rf�M���� �����\"�vK1{�Ծ�<ᩩm�L�hp�\n�w�'��\r-�(q��r���o���aN7 ��#�id�d����f�(�aY�y\nNqY�I E�Q�!����1QV��p��N\$��R#�b�Ma\0�|�l����9�VO���PI�!Pb2	��Ph���%\"�!�(Vb-R8�)�L�e ���%�(�(�qđT<�XX�{)e�������d Rfڲ���(�+ҵ��̄�*2�,�}e\\\$��\\���x�����e+f�<�0V�Ѓ/�!/ΐMd�g���/'4Ȇ�%P�X0��'s\"5f��J&:�������;��),sD!��*�밭!�	�:�bУt�~҄�\0�Jo�A&�z�Ү\$�C\0k�/O�4��W\"\n���Zh�/���g)���qD4��\$�v���h�0�b��#_%\n/�t]M�8Pk6E�8�6�vg���1,�m�SfNe��J�D�\n��z�j\rm��D��~�bU1Df�\n��î��n��Jz@�˭H���M��t)4@�ϨL\"H7:�#�7�l~sѺ8S�;��D�SD�W�}\n�}#x������mV��h ��\"i2��SH0�l�j�sR�-�'8�`@}O�*��I����K�ǁ^0���j����Ŗ\$�v �N��)�44i�ɨ!O��I�H!4I%vL-��*���8�c:[e�-�q���F* ";
            break;
        case"zh":
            $f = "�^��s�\\�r����|%��:�\$\nr.���2�r/d�Ȼ[8� S�8�r�!T�\\�s���I4�b�r��ЀJs!Kd�u�e�V���D�X,#!��j6� �:�t\nr���U:.Z�Pˑ.�\rVWd^%�䌵�r�T�Լ�*�s#U�`Qd�u'c(��oF����e3�Nb�`�p2N�S��ӣ:LY�ta~��&6ۊ��r�s���k��{��6�������c(��2��f�q�ЈP:S*@S�^�t*���ΔT���^\\�nNG#y�j\"5M��9�� ��2�x�m8���c�9����ڼź\0�>A~L���6s%I�X��ˊ�:��M(�bx���d�*�b�K��aL��K#�s��X�g)<��<v��q>s��K���tFC���D�!zH�\$��C�*r�e��^�@P�׶L��ѿ���:��8A<��(�ՍØ�7Э��K����Y�n\n�)�QBr�4t�\"�\$j�W��9@@��;��D��L�%�*ݜ�asՓ}^ ��k��4��ph��PX�1�m�\r{\0�P*;�-kc30�*9�`@Y�@�2���D4���9�Ax^;�p�\\�o��3��(��C�)��\r��*�����P�px�!�jt�I�E�2k�%��|s��ӂI�����1(\\�9\r6�FU�.�Q`r�e�5S�B��9\r���9�� A�RYE��D�&eA�C�O)U�QPr�D��G�W �:��X�0����b��13����M������ZN)9vs�zF���\\���u�<J��,��qXsc�t��ì��#l�cI>A��7;\"��r�(�v�D���^=nSx�9YNW��G-S�w�E.^l�����7o�xFHSGAMR���S��:��g�R�~����?���H��Lkf�s��3\r��j9�����*�0��py�B�U���3d�7�s���tP(0�p�w �]��j�@s/�¤�S\nAqH9�H�*�LF�Fn#ċ&��r��*��H�tE	���4l\$9@�������o5��\"�\\Eq�V�>@��:4&�M�SYL5��d<!ґK��[��|�lG.�5����W��?!�8�ƅ��ጫ�t.�ػ��w^K�7�\\����PJC(���Hm|6�����w�k4cK,�k`�D�@�]c��h��B��B.�@�d\$W���K\"��X�a-@����*J֌C4�A�9d��\"頤?P֚@�eC�a���1��\\[@s���\"DL�yq�XJ\n�b��� \n (	���X�������CI�j\"N�8ڳ%��3�x�V����5�%cMp��4,0�!���Nc@��ܗd`I�7'\"�a\\-X�_*�e�	�(��	\$L<�p�Z2�A��[�F�Ëjh,3��d�5��zAD	;�Z4*(R5�Hp�HP	�L*P�Lb����H��^�\0���>Y�\n�3�vB��� ������f�\0�b�Su��3R@@gVpF\n�5մ`�*��!�����#(�@��	Îa@��x�A<'\0� A\n���P�B`E�e�V�z�-T�h�	�]��%E��ͱ��k�tΩt�<�\"0.Qw�E�\"�)���v��J������:F�|�;��Lcm~8U>G�_�R]�E���9Dx�k�E�\0��8��pV\"�./d5=�:��\n����39\"|L����Ķߎ[�yY�S\r!�ѹU	�\\r3�z��\nyH��\0J�מc\$\$���\"4c:C��e�� (����{�fm�Qf�Z.��dl�Pw�p�0�I\"(Pv%;-�`H��ミ0��qC�6.�0�\"L_a`�۶|X��<��\$0�q�����4F4H�*�@��@ �'�4��~ej�¥Ė��Jy�k�Be��\0^6-�׺�W�\$H)�Q�b�JǨF6���X�Q&#Q���vJL�W��Q���d��퀍E�Kk)LD�C�����(&Z���wh�̔�B(\nQ=)�Ã�a���r2����F����=���)m\r��1C�B�r��Ġ�J��r�89�k}b�1�C��0�-9���9���<e��Ԅ��g��^aϜ��b�t�]���:=XW#���z��9���.���C�|�֜DA ��@�b9��0M� ��9������e�1�4���/�������; �͙��y1x%R:I��.����.�5-�����/��0��)�U��G�ih���/O�2~VyGؼ�w������X9������t|Y���5���sona�~���_��s����/����X�B3��A�f\$�I\"P>�.O�'OG̴��*aP��|���>����E�pO,Mc~#!(�	��k�\$�7�r�t�������\"�k��i� ��fe�^M���,����PTft�fsd����MH)����z%��P�%\"��0�;��MJs�p-��dg�Ra^y!\\�as� �t70t���@��f*��O�/�O�qc�'�n#����0����L����&a<��6��qeo�P�V�Q�Q1��g1%\0����qg1?1*�O-�S�:�MЍ\rX�Q\\S1B�-бi����0�Qz>.VJ�	-�c��Ao���.��@i�������{B�A>�M�:�\$k��+r)��-\n\"l\"��������0�af�zۭ��H4\r�Vg�`�O��Y��m#X��ު�́E�6������Ƴ�?\0�\n���pOH���6���,�0P�#B8�c���j	�\r2Af��.!�C�裒�c�(#��m*��:QK&/1��\r�8`��5#bߒ�\$C���j�\$⧢�H�\nA<Lβ�\"��i�����X�N�%��I��oq��.�O�F2C)#\n�\r����tˤ0���c\0EA�o,�z\"����,L ���,����8�\rN1R�J���\nŎ��\r�0���,�FJ�!�r���P&%<�-c�.�K�n�X����.+��=k��rSE\0b��d�@�	\0t	��@�\n`";
            break;
        case"zh-tw":
            $f = "�^��%ӕ\\�r�����|%��u:H�B(\\�4��p�r��neRQ̡D8� S�\n�t*.t�I&�G�N��AʤS�V�:	t%9��Sy:\"<�r�ST�,#!��j6�1uL\0�����U:.��I9���B��K&]\nD�X�[��}-,�r����������&��a;D�x��r4��&�)��s3�S���t�\r�A��b���E�E1��ԣ�g:�x�]#0,'}üb1Q�\\y\0�V��E<���g��S� )ЪOLP\0��Δ�MƼ��� 2��F��׶����{N͍�@9�����;��#ttn�z�>��D�ql����@g1&Z%�)�T'9jB\nC\"�%)n�j�\"����d�Co{@�IBO���s�Ā���*�O���t�ě��\$d���lY�\nr�%�\0J�B#h۴��P�?t��)�>���`7cH�B7P�.��˪��Vs�e�dX���t�*ʬr���L��)^C kE�4�V��%�\\R�e�pr\$)�X�`P�2\r���@�;9A��9AC�1��+�6\$\0001@L��4�L���BP��2�\0yj���3��:����x�{��\r�a?�����p_�p�*�J@|6��Ӽ3?�k_C\r��^0����@�QU� T�W��!u2���q�J)!Dtĵ�eن@�/�3�O%tM��P�0�Cs�3�hx�C�`aK�K�R\\��&%��S�o1U�Y+�ɌT�u�#����:��9�%�9{(6C����ℷ��vs�|�s��)�GT��*�UA�\\z�x� �P�@�GI*[�%2[� �#m�cS=�c�7;b��r�%���I'.-�+x�gys���nt�U�fQ�y%�O�~�k��~�\$sŷ�����z���}zr��)]%����z��o�U�\"��K��elm��1�xr2FQj.���0lXF�]�'��y�bA�<�\0���Z!��\0��9�k�:(:C8a;���V��CpujmV\n��\"	�4aL)cR�[/�`E��#N�������%�r7�E	�*��ё\n�\\��� �����_Ã�C�P��a�s,��c	`� T�[c�)�(вcb9+��\"tP0�|B�S\"P���.3��i]HYs.��Wz�^k�{�u��q����0'P�T*�a(\$���ik�'`A0V�O5S0Ęh���RC(y/�H��Q\n��%b���A��,�5��lv�����O!�M �B�,\$vО�u�l�L�Aa�`�\0�(�inA���V�Qz1Ff0��.�{/�G�H�@PEH�B��`��! ��\"�<�ri̳>hM��=e�CPmB͝A�;Ӻ���Dt\n�\\J�0�I\"G�`L�\n@¸Z�ȜQ	�r���x��LJLB�U\0��\0y���4������6f�u��Pf?a�6K�l�\r����\"i��P	�L*�)!@/�|M�*��D���\$V��Z�4A&\"����T��wG��Bu���i�aM�\0�NA�Z�*��xi��>g��^��?��R	a�/�0��ԥ�BP�(O	��*�\0�B�EW�@�-�j�2�Q ����ɑ:�Z݅�A����C�v�:\"�����ȸ�g����+	��2/�D���&E!���y�?W�\"�*���H*�P\"ܲ�b�E7�.\"�\0����q\"��!�i>�\"�\"����҄�0���]d2�Ȣy�0b%^�k�x5�f�L4���L%�:n��S:vxp��#kڬ�\0^�ؒ��Ш%��y��ܥ��f�_�w*ÜZ\n!�/��e�;Fh� {.��B@��D^&^����(��k�v��_Hr��L��@��bd^jҬ9���I\"\0Q���/����^9{��ӗ��ꋱm�T~����Ԍ:B�\n�P �0�+�}@� �Wu�Oj1K�t\n�E�x�/��Xlx i�<���nc�6�ݢ�'��\r�}�D\"�X��>/�sx]�K����G��*G/\"1��N�x�G��F\0��r�� ?aݦ���G��Ф㛒E\"G8�sro8����ÑM)�l��P�=1M��:*9�O�;���f�.��KC��aZ(�h�I\"��2nýI�5&F\r�!\nX�I8�����(�\"\rF�����t��{\nH��&š��C��q�dtkO�h���t��1\0/�C�H�\$�E��^�I�U�Te���<J��ȅ\0��h��C��?<.\0F�f������'��%1�	j;��SF�C#�}�JI�!ER�T�Ц���H���0�;���H�#�\r��_��mLQhw��oOh���7��<�D�A�ͦ���\"��OZy�\0�~-j�^��bGo���n�� ð�\rJ�벻p�J��P���W�j20L�B|D�C���PU�p��\\�*���+@�F��¨(e�zQBt���J*D�P�\r 8b<�hH�����B�	�~��L�\r;\nB��MB�c��@�\$��df�l/�\raI-�\\��oF����f�D�-�١m'��1��(��i�2��^ڭ��D�`eg���^r� ��B|�yDy��͓F`*!!\0���}pToLl�ar�m����ģ���#}�Zd�\$P�����bD����t1���1�<��\0�.���a�����-dՅ=mUQ���h���y�� m]�b�mj�ĲKd�K�!������r,H�3�H�.I\"	���\"�^r̀}'�L(��N\0�?i�`P4�R�ɺFA���Hc�F�.z%�b�E.�~����4�!D!1>��^���`Ƃ@�\rd�>�n&�6(4\r���:[�tˀ\r���a��\n���Z�*�;��G⒮�o���8�hݤ�\0�-��-�D�8�0�69n�L-z{�M\"	�޶���\\6�]4�<�\"���C�6\"�,r.g��d������O,z,8�H�dv�.�(a�(��9c�W��OcP2�3.�\r�����.�pu�*���\np�Z�l�X����ο��>B�8N��,��\nŜ��\r�f\0g�φ|�8fNV�N�|.�WB�<ӎ�d>1\n�lSCl\$<ӡ:Q�ra%p���[!�K��	\0t	��@�\n`";
            break;
    }
    $qi = [];
    foreach (explode("\n", lzw_decompress($f)) as $X) {
        $qi[] = (strpos($X, "\t") ? explode("\t", $X) : $X);
    }

    return $qi;
}

if (!$qi) {
    $qi = get_translations($ca);
    $_SESSION["translations"] = $qi;
}
if (extension_loaded('pdo')) {
    class Min_PDO extends
        PDO
    {
        public $_result;

        public $server_info;

        public $affected_rows;

        public $errno;

        public $error;

        public function __construct()
        {
            global $b;
            $bg = array_search("SQL", $b->operators);
            if ($bg !== false) {
                unset($b->operators[$bg]);
            }
        }

        public function dsn($kc, $V, $F, $vf = [])
        {
            try {
                parent::__construct($kc, $V, $F, $vf);
            } catch (Exception$Bc) {
                auth_error(h($Bc->getMessage()));
            }
            $this->setAttribute(13, ['Min_PDOStatement']);
            $this->server_info = @$this->getAttribute(4);
        }

        public function query($G, $_i = false)
        {
            $H = parent::query($G);
            $this->error = "";
            if (!$H) {
                list(, $this->errno, $this->error) = $this->errorInfo();
                if (!$this->error) {
                    $this->error = lang(21);
                }

                return
                    false;
            }
            $this->store_result($H);

            return $H;
        }

        public function multi_query($G)
        {
            return $this->_result = $this->query($G);
        }

        public function store_result($H = null)
        {
            if (!$H) {
                $H = $this->_result;
                if (!$H) {
                    return
                    false;
                }
            }
            if ($H->columnCount()) {
                $H->num_rows = $H->rowCount();

                return $H;
            }
            $this->affected_rows = $H->rowCount();

            return
                true;
        }

        public function next_result()
        {
            if (!$this->_result) {
                return
                false;
            }
            $this->_result->_offset = 0;

            return @$this->_result->nextRowset();
        }

        public function result($G, $o = 0)
        {
            $H = $this->query($G);
            if (!$H) {
                return
                false;
            }
            $J = $H->fetch();

            return $J[$o];
        }
    }

    class Min_PDOStatement extends
        PDOStatement
    {
        public $_offset = 0;

        public $num_rows;

        public function fetch_assoc()
        {
            return $this->fetch(2);
        }

        public function fetch_row()
        {
            return $this->fetch(3);
        }

        public function fetch_field()
        {
            $J = (object)$this->getColumnMeta($this->_offset++);
            $J->orgtable = $J->table;
            $J->orgname = $J->name;
            $J->charsetnr = (in_array("blob", (array)$J->flags) ? 63 : 0);

            return $J;
        }
    }
}
$fc = [];

class Min_SQL
{
    public $_conn;

    public function __construct($g)
    {
        $this->_conn = $g;
    }

    public function select($R, $L, $Z, $od, $xf = [], $z = 1, $E = 0, $jg = false)
    {
        global $b, $x;
        $Xd = (count($od) < count($L));
        $G = $b->selectQueryBuild($L, $Z, $od, $xf, $z, $E);
        if (!$G) {
            $G = "SELECT" . limit(($_GET["page"] != "last" && $z != "" && $od && $Xd && $x == "sql" ? "SQL_CALC_FOUND_ROWS " : "") . implode(", ", $L) . "\nFROM " . table($R), ($Z ? "\nWHERE " . implode(" AND ", $Z) : "") . ($od && $Xd ? "\nGROUP BY " . implode(", ", $od) : "") . ($xf ? "\nORDER BY " . implode(", ", $xf) : ""), ($z != "" ? +$z : null), ($E ? $z * $E : 0), "\n");
        }
        $_h = microtime(true);
        $I = $this->_conn->query($G);
        if ($jg) {
            echo $b->selectQuery($G, $_h, !$I);
        }

        return $I;
    }

    public function delete($R, $tg, $z = 0)
    {
        $G = "FROM " . table($R);

        return
            queries("DELETE" . ($z ? limit1($R, $G, $tg) : " $G$tg"));
    }

    public function update($R, $O, $tg, $z = 0, $M = "\n")
    {
        $Ri = [];
        foreach ($O
                 as $y => $X) {
            $Ri[] = "$y = $X";
        }
        $G = table($R) . " SET$M" . implode(",$M", $Ri);

        return
            queries("UPDATE" . ($z ? limit1($R, $G, $tg, $M) : " $G$tg"));
    }

    public function insert($R, $O)
    {
        return
            queries("INSERT INTO " . table($R) . ($O ? " (" . implode(", ", array_keys($O)) . ")\nVALUES (" . implode(", ", $O) . ")" : " DEFAULT VALUES"));
    }

    public function insertUpdate($R, $K, $hg)
    {
        return
            false;
    }

    public function begin()
    {
        return
            queries("BEGIN");
    }

    public function commit()
    {
        return
            queries("COMMIT");
    }

    public function rollback()
    {
        return
            queries("ROLLBACK");
    }

    public function slowQuery($G, $bi)
    {
    }

    public function convertSearch($u, $X, $o)
    {
        return $u;
    }

    public function value($X, $o)
    {
        return (method_exists($this->_conn, 'value') ? $this->_conn->value($X, $o) : (is_resource($X) ? stream_get_contents($X) : $X));
    }

    public function quoteBinary($Vg)
    {
        return
            q($Vg);
    }

    public function warnings()
    {
        return '';
    }

    public function tableHelp($C)
    {
    }
}

$fc["sqlite"] = "SQLite 3";
$fc["sqlite2"] = "SQLite 2";
if (isset($_GET["sqlite"]) || isset($_GET["sqlite2"])) {
    $eg = [(isset($_GET["sqlite"]) ? "SQLite3" : "SQLite"), "PDO_SQLite"];
    define("DRIVER", (isset($_GET["sqlite"]) ? "sqlite" : "sqlite2"));
    if (class_exists(isset($_GET["sqlite"]) ? "SQLite3" : "SQLiteDatabase")) {
        if (isset($_GET["sqlite"])) {
            class Min_SQLite
            {
                public $extension = "SQLite3";

                public $server_info;

                public $affected_rows;

                public $errno;

                public $error;

                public $_link;

                public function __construct($Vc)
                {
                    $this->_link = new
                    SQLite3($Vc);
                    $Ui = $this->_link->version();
                    $this->server_info = $Ui["versionString"];
                }

                public function query($G)
                {
                    $H = @$this->_link->query($G);
                    $this->error = "";
                    if (!$H) {
                        $this->errno = $this->_link->lastErrorCode();
                        $this->error = $this->_link->lastErrorMsg();

                        return
                            false;
                    } elseif ($H->numColumns()) {
                        return
                        new
                        Min_Result($H);
                    }
                    $this->affected_rows = $this->_link->changes();

                    return
                        true;
                }

                public function quote($Q)
                {
                    return (is_utf8($Q) ? "'" . $this->_link->escapeString($Q) . "'" : "x'" . reset(unpack('H*', $Q)) . "'");
                }

                public function store_result()
                {
                    return $this->_result;
                }

                public function result($G, $o = 0)
                {
                    $H = $this->query($G);
                    if (!is_object($H)) {
                        return
                        false;
                    }
                    $J = $H->_result->fetchArray();

                    return $J[$o];
                }
            }

            class Min_Result
            {
                public $_result;

                public $_offset = 0;

                public $num_rows;

                public function __construct($H)
                {
                    $this->_result = $H;
                }

                public function fetch_assoc()
                {
                    return $this->_result->fetchArray(SQLITE3_ASSOC);
                }

                public function fetch_row()
                {
                    return $this->_result->fetchArray(SQLITE3_NUM);
                }

                public function fetch_field()
                {
                    $d = $this->_offset++;
                    $U = $this->_result->columnType($d);

                    return (object)["name" => $this->_result->columnName($d), "type" => $U, "charsetnr" => ($U == SQLITE3_BLOB ? 63 : 0),];
                }

                public function __desctruct()
                {
                    return $this->_result->finalize();
                }
            }
        } else {
            class Min_SQLite
            {
                public $extension = "SQLite";

                public $server_info;

                public $affected_rows;

                public $error;

                public $_link;

                public function __construct($Vc)
                {
                    $this->server_info = sqlite_libversion();
                    $this->_link = new
                    SQLiteDatabase($Vc);
                }

                public function query($G, $_i = false)
                {
                    $Qe = ($_i ? "unbufferedQuery" : "query");
                    $H = @$this->_link->$Qe($G, SQLITE_BOTH, $n);
                    $this->error = "";
                    if (!$H) {
                        $this->error = $n;

                        return
                            false;
                    } elseif ($H === true) {
                        $this->affected_rows = $this->changes();

                        return
                            true;
                    }

                    return
                        new
                        Min_Result($H);
                }

                public function quote($Q)
                {
                    return "'" . sqlite_escape_string($Q) . "'";
                }

                public function store_result()
                {
                    return $this->_result;
                }

                public function result($G, $o = 0)
                {
                    $H = $this->query($G);
                    if (!is_object($H)) {
                        return
                        false;
                    }
                    $J = $H->_result->fetch();

                    return $J[$o];
                }
            }

            class Min_Result
            {
                public $_result;

                public $_offset = 0;

                public $num_rows;

                public function __construct($H)
                {
                    $this->_result = $H;
                    if (method_exists($H, 'numRows')) {
                        $this->num_rows = $H->numRows();
                    }
                }

                public function fetch_assoc()
                {
                    $J = $this->_result->fetch(SQLITE_ASSOC);
                    if (!$J) {
                        return
                        false;
                    }
                    $I = [];
                    foreach ($J
                             as $y => $X) {
                        $I[($y[0] == '"' ? idf_unescape($y) : $y)] = $X;
                    }

                    return $I;
                }

                public function fetch_row()
                {
                    return $this->_result->fetch(SQLITE_NUM);
                }

                public function fetch_field()
                {
                    $C = $this->_result->fieldName($this->_offset++);
                    $Xf = '(\[.*]|"(?:[^"]|"")*"|(.+))';
                    if (preg_match("~^($Xf\\.)?$Xf\$~", $C, $B)) {
                        $R = ($B[3] != "" ? $B[3] : idf_unescape($B[2]));
                        $C = ($B[5] != "" ? $B[5] : idf_unescape($B[4]));
                    }

                    return (object)["name" => $C, "orgname" => $C, "orgtable" => $R,];
                }
            }
        }
    } elseif (extension_loaded("pdo_sqlite")) {
        class Min_SQLite extends
            Min_PDO
        {
            public $extension = "PDO_SQLite";

            public function __construct($Vc)
            {
                $this->dsn(DRIVER . ":$Vc", "", "");
            }
        }
    }
    if (class_exists("Min_SQLite")) {
        class Min_DB extends
            Min_SQLite
        {
            public function __construct()
            {
                parent::__construct(":memory:");
                $this->query("PRAGMA foreign_keys = 1");
            }

            public function select_db($Vc)
            {
                if (is_readable($Vc) && $this->query("ATTACH " . $this->quote(preg_match("~(^[/\\\\]|:)~", $Vc) ? $Vc : dirname($_SERVER["SCRIPT_FILENAME"]) . "/$Vc") . " AS a")) {
                    parent::__construct($Vc);
                    $this->query("PRAGMA foreign_keys = 1");

                    return
                        true;
                }

                return
                    false;
            }

            public function multi_query($G)
            {
                return $this->_result = $this->query($G);
            }

            public function next_result()
            {
                return
                    false;
            }
        }
    }

    class Min_Driver extends
        Min_SQL
    {
        public function insertUpdate($R, $K, $hg)
        {
            $Ri = [];
            foreach ($K
                     as $O) {
                $Ri[] = "(" . implode(", ", $O) . ")";
            }

            return
                queries("REPLACE INTO " . table($R) . " (" . implode(", ", array_keys(reset($K))) . ") VALUES\n" . implode(",\n", $Ri));
        }

        public function tableHelp($C)
        {
            if ($C == "sqlite_sequence") {
                return "fileformat2.html#seqtab";
            }
            if ($C == "sqlite_master") {
                return "fileformat2.html#$C";
            }
        }
    }

    function idf_escape($u)
    {
        return '"' . str_replace('"', '""', $u) . '"';
    }

    function table($u)
    {
        return
            idf_escape($u);
    }

    function connect()
    {
        global $b;
        list(, , $F) = $b->credentials();
        if ($F != "") {
            return
            lang(22);
        }

        return
            new
            Min_DB;
    }

    function get_databases()
    {
        return
            [];
    }

    function limit($G, $Z, $z, $D = 0, $M = " ")
    {
        return " $G$Z" . ($z !== null ? $M . "LIMIT $z" . ($D ? " OFFSET $D" : "") : "");
    }

    function limit1($R, $G, $Z, $M = "\n")
    {
        global $g;

        return (preg_match('~^INTO~', $G) || $g->result("SELECT sqlite_compileoption_used('ENABLE_UPDATE_DELETE_LIMIT')") ? limit($G, $Z, 1, 0, $M) : " $G WHERE rowid = (SELECT rowid FROM " . table($R) . $Z . $M . "LIMIT 1)");
    }

    function db_collation($l, $qb)
    {
        global $g;

        return $g->result("PRAGMA encoding");
    }

    function engines()
    {
        return
            [];
    }

    function logged_user()
    {
        return
            get_current_user();
    }

    function tables_list()
    {
        return
            get_key_vals("SELECT name, type FROM sqlite_master WHERE type IN ('table', 'view') ORDER BY (name = 'sqlite_sequence'), name");
    }

    function count_tables($k)
    {
        return
            [];
    }

    function table_status($C = "")
    {
        global $g;
        $I = [];
        foreach (get_rows("SELECT name AS Name, type AS Engine, 'rowid' AS Oid, '' AS Auto_increment FROM sqlite_master WHERE type IN ('table', 'view') " . ($C != "" ? "AND name = " . q($C) : "ORDER BY name")) as $J) {
            $J["Rows"] = $g->result("SELECT COUNT(*) FROM " . idf_escape($J["Name"]));
            $I[$J["Name"]] = $J;
        }
        foreach (get_rows("SELECT * FROM sqlite_sequence", null, "") as $J) {
            $I[$J["name"]]["Auto_increment"] = $J["seq"];
        }

        return ($C != "" ? $I[$C] : $I);
    }

    function is_view($S)
    {
        return $S["Engine"] == "view";
    }

    function fk_support($S)
    {
        global $g;

        return !$g->result("SELECT sqlite_compileoption_used('OMIT_FOREIGN_KEY')");
    }

    function fields($R)
    {
        global $g;
        $I = [];
        $hg = "";
        foreach (get_rows("PRAGMA table_info(" . table($R) . ")") as $J) {
            $C = $J["name"];
            $U = strtolower($J["type"]);
            $Tb = $J["dflt_value"];
            $I[$C] = ["field" => $C, "type" => (preg_match('~int~i', $U) ? "integer" : (preg_match('~char|clob|text~i', $U) ? "text" : (preg_match('~blob~i', $U) ? "blob" : (preg_match('~real|floa|doub~i', $U) ? "real" : "numeric")))), "full_type" => $U, "default" => (preg_match("~'(.*)'~", $Tb, $B) ? str_replace("''", "'", $B[1]) : ($Tb == "NULL" ? null : $Tb)), "null" => !$J["notnull"], "privileges" => ["select" => 1, "insert" => 1, "update" => 1], "primary" => $J["pk"],];
            if ($J["pk"]) {
                if ($hg != "") {
                    $I[$hg]["auto_increment"] = false;
                } elseif (preg_match('~^integer$~i', $U)) {
                    $I[$C]["auto_increment"] = true;
                }
                $hg = $C;
            }
        }
        $vh = $g->result("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = " . q($R));
        preg_match_all('~(("[^"]*+")+|[a-z0-9_]+)\s+text\s+COLLATE\s+(\'[^\']+\'|\S+)~i', $vh, $Ce, PREG_SET_ORDER);
        foreach ($Ce
                 as $B) {
            $C = str_replace('""', '"', preg_replace('~^"|"$~', '', $B[1]));
            if ($I[$C]) {
                $I[$C]["collation"] = trim($B[3], "'");
            }
        }

        return $I;
    }

    function indexes($R, $h = null)
    {
        global $g;
        if (!is_object($h)) {
            $h = $g;
        }
        $I = [];
        $vh = $h->result("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = " . q($R));
        if (preg_match('~\bPRIMARY\s+KEY\s*\((([^)"]+|"[^"]*"|`[^`]*`)++)~i', $vh, $B)) {
            $I[""] = ["type" => "PRIMARY", "columns" => [], "lengths" => [], "descs" => []];
            preg_match_all('~((("[^"]*+")+|(?:`[^`]*+`)+)|(\S+))(\s+(ASC|DESC))?(,\s*|$)~i', $B[1], $Ce, PREG_SET_ORDER);
            foreach ($Ce
                     as $B) {
                $I[""]["columns"][] = idf_unescape($B[2]) . $B[4];
                $I[""]["descs"][] = (preg_match('~DESC~i', $B[5]) ? '1' : null);
            }
        }
        if (!$I) {
            foreach (fields($R) as $C => $o) {
                if ($o["primary"]) {
                    $I[""] = ["type" => "PRIMARY", "columns" => [$C], "lengths" => [], "descs" => [null]];
                }
            }
        }
        $yh = get_key_vals("SELECT name, sql FROM sqlite_master WHERE type = 'index' AND tbl_name = " . q($R), $h);
        foreach (get_rows("PRAGMA index_list(" . table($R) . ")", $h) as $J) {
            $C = $J["name"];
            $v = ["type" => ($J["unique"] ? "UNIQUE" : "INDEX")];
            $v["lengths"] = [];
            $v["descs"] = [];
            foreach (get_rows("PRAGMA index_info(" . idf_escape($C) . ")", $h) as $Ug) {
                $v["columns"][] = $Ug["name"];
                $v["descs"][] = null;
            }
            if (preg_match('~^CREATE( UNIQUE)? INDEX ' . preg_quote(idf_escape($C) . ' ON ' . idf_escape($R), '~') . ' \((.*)\)$~i', $yh[$C], $Eg)) {
                preg_match_all('/("[^"]*+")+( DESC)?/', $Eg[2], $Ce);
                foreach ($Ce[2] as $y => $X) {
                    if ($X) {
                        $v["descs"][$y] = '1';
                    }
                }
            }
            if (!$I[""] || $v["type"] != "UNIQUE" || $v["columns"] != $I[""]["columns"] || $v["descs"] != $I[""]["descs"] || !preg_match("~^sqlite_~", $C)) {
                $I[$C] = $v;
            }
        }

        return $I;
    }

    function foreign_keys($R)
    {
        $I = [];
        foreach (get_rows("PRAGMA foreign_key_list(" . table($R) . ")") as $J) {
            $q =& $I[$J["id"]];
            if (!$q) {
                $q = $J;
            }
            $q["source"][] = $J["from"];
            $q["target"][] = $J["to"];
        }

        return $I;
    }

    function view($C)
    {
        global $g;

        return
            ["select" => preg_replace('~^(?:[^`"[]+|`[^`]*`|"[^"]*")* AS\s+~iU', '', $g->result("SELECT sql FROM sqlite_master WHERE name = " . q($C)))];
    }

    function collations()
    {
        return (isset($_GET["create"]) ? get_vals("PRAGMA collation_list", 1) : []);
    }

    function information_schema($l)
    {
        return
            false;
    }

    function error()
    {
        global $g;

        return
            h($g->error);
    }

    function check_sqlite_name($C)
    {
        global $g;
        $Lc = "db|sdb|sqlite";
        if (!preg_match("~^[^\\0]*\\.($Lc)\$~", $C)) {
            $g->error = lang(23, str_replace("|", ", ", $Lc));

            return
                false;
        }

        return
            true;
    }

    function create_database($l, $pb)
    {
        global $g;
        if (file_exists($l)) {
            $g->error = lang(24);

            return
                false;
        }
        if (!check_sqlite_name($l)) {
            return
            false;
        }
        try {
            $_ = new
            Min_SQLite($l);
        } catch (Exception$Bc) {
            $g->error = $Bc->getMessage();

            return
                false;
        }
        $_->query('PRAGMA encoding = "UTF-8"');
        $_->query('CREATE TABLE adminer (i)');
        $_->query('DROP TABLE adminer');

        return
            true;
    }

    function drop_databases($k)
    {
        global $g;
        $g->__construct(":memory:");
        foreach ($k
                 as $l) {
            if (!@unlink($l)) {
                $g->error = lang(24);

                return
                    false;
            }
        }

        return
            true;
    }

    function rename_database($C, $pb)
    {
        global $g;
        if (!check_sqlite_name($C)) {
            return
            false;
        }
        $g->__construct(":memory:");
        $g->error = lang(24);

        return @rename(DB, $C);
    }

    function auto_increment()
    {
        return " PRIMARY KEY" . (DRIVER == "sqlite" ? " AUTOINCREMENT" : "");
    }

    function alter_table($R, $C, $p, $dd, $vb, $vc, $pb, $Ma, $Rf)
    {
        $Li = ($R == "" || $dd);
        foreach ($p
                 as $o) {
            if ($o[0] != "" || !$o[1] || $o[2]) {
                $Li = true;
                break;
            }
        }
        $c = [];
        $Ff = [];
        foreach ($p
                 as $o) {
            if ($o[1]) {
                $c[] = ($Li ? $o[1] : "ADD " . implode($o[1]));
                if ($o[0] != "") {
                    $Ff[$o[0]] = $o[1][0];
                }
            }
        }
        if (!$Li) {
            foreach ($c
                     as $X) {
                if (!queries("ALTER TABLE " . table($R) . " $X")) {
                    return
                    false;
                }
            }
            if ($R != $C && !queries("ALTER TABLE " . table($R) . " RENAME TO " . table($C))) {
                return
                false;
            }
        } elseif (!recreate_table($R, $C, $c, $Ff, $dd)) {
            return
            false;
        }
        if ($Ma) {
            queries("UPDATE sqlite_sequence SET seq = $Ma WHERE name = " . q($C));
        }

        return
            true;
    }

    function recreate_table($R, $C, $p, $Ff, $dd, $w = [])
    {
        if ($R != "") {
            if (!$p) {
                foreach (fields($R) as $y => $o) {
                    if ($w) {
                        $o["auto_increment"] = 0;
                    }
                    $p[] = process_field($o, $o);
                    $Ff[$y] = idf_escape($y);
                }
            }
            $ig = false;
            foreach ($p
                     as $o) {
                if ($o[6]) {
                    $ig = true;
                }
            }
            $ic = [];
            foreach ($w
                     as $y => $X) {
                if ($X[2] == "DROP") {
                    $ic[$X[1]] = true;
                    unset($w[$y]);
                }
            }
            foreach (indexes($R) as $fe => $v) {
                $e = [];
                foreach ($v["columns"] as $y => $d) {
                    if (!$Ff[$d]) {
                        continue
                    2;
                    }
                    $e[] = $Ff[$d] . ($v["descs"][$y] ? " DESC" : "");
                }
                if (!$ic[$fe]) {
                    if ($v["type"] != "PRIMARY" || !$ig) {
                        $w[] = [$v["type"], $fe, $e];
                    }
                }
            }
            foreach ($w
                     as $y => $X) {
                if ($X[0] == "PRIMARY") {
                    unset($w[$y]);
                    $dd[] = "  PRIMARY KEY (" . implode(", ", $X[2]) . ")";
                }
            }
            foreach (foreign_keys($R) as $fe => $q) {
                foreach ($q["source"] as $y => $d) {
                    if (!$Ff[$d]) {
                        continue
                    2;
                    }
                    $q["source"][$y] = idf_unescape($Ff[$d]);
                }
                if (!isset($dd[" $fe"])) {
                    $dd[] = " " . format_foreign_key($q);
                }
            }
            queries("BEGIN");
        }
        foreach ($p
                 as $y => $o) {
            $p[$y] = "  " . implode($o);
        }
        $p = array_merge($p, array_filter($dd));
        if (!queries("CREATE TABLE " . table($R != "" ? "adminer_$C" : $C) . " (\n" . implode(",\n", $p) . "\n)")) {
            return
            false;
        }
        if ($R != "") {
            if ($Ff && !queries("INSERT INTO " . table("adminer_$C") . " (" . implode(", ", $Ff) . ") SELECT " . implode(", ", array_map('idf_escape', array_keys($Ff))) . " FROM " . table($R))) {
                return
                false;
            }
            $wi = [];
            foreach (triggers($R) as $ui => $ci) {
                $ti = trigger($ui);
                $wi[] = "CREATE TRIGGER " . idf_escape($ui) . " " . implode(" ", $ci) . " ON " . table($C) . "\n$ti[Statement]";
            }
            if (!queries("DROP TABLE " . table($R))) {
                return
                false;
            }
            queries("ALTER TABLE " . table("adminer_$C") . " RENAME TO " . table($C));
            if (!alter_indexes($C, $w)) {
                return
                false;
            }
            foreach ($wi
                     as $ti) {
                if (!queries($ti)) {
                    return
                    false;
                }
            }
            queries("COMMIT");
        }

        return
            true;
    }

    function index_sql($R, $U, $C, $e)
    {
        return "CREATE $U " . ($U != "INDEX" ? "INDEX " : "") . idf_escape($C != "" ? $C : uniqid($R . "_")) . " ON " . table($R) . " $e";
    }

    function alter_indexes($R, $c)
    {
        foreach ($c
                 as $hg) {
            if ($hg[0] == "PRIMARY") {
                return
                recreate_table($R, $R, [], [], [], $c);
            }
        }
        foreach (array_reverse($c) as $X) {
            if (!queries($X[2] == "DROP" ? "DROP INDEX " . idf_escape($X[1]) : index_sql($R, $X[0], $X[1], "(" . implode(", ", $X[2]) . ")"))) {
                return
                false;
            }
        }

        return
            true;
    }

    function truncate_tables($T)
    {
        return
            apply_queries("DELETE FROM", $T);
    }

    function drop_views($Wi)
    {
        return
            apply_queries("DROP VIEW", $Wi);
    }

    function drop_tables($T)
    {
        return
            apply_queries("DROP TABLE", $T);
    }

    function move_tables($T, $Wi, $Th)
    {
        return
            false;
    }

    function trigger($C)
    {
        global $g;
        if ($C == "") {
            return
            ["Statement" => "BEGIN\n\t;\nEND"];
        }
        $u = '(?:[^`"\s]+|`[^`]*`|"[^"]*")+';
        $vi = trigger_options();
        preg_match("~^CREATE\\s+TRIGGER\\s*$u\\s*(" . implode("|", $vi["Timing"]) . ")\\s+([a-z]+)(?:\\s+OF\\s+($u))?\\s+ON\\s*$u\\s*(?:FOR\\s+EACH\\s+ROW\\s)?(.*)~is", $g->result("SELECT sql FROM sqlite_master WHERE type = 'trigger' AND name = " . q($C)), $B);
        $gf = $B[3];

        return
            ["Timing" => strtoupper($B[1]), "Event" => strtoupper($B[2]) . ($gf ? " OF" : ""), "Of" => ($gf[0] == '`' || $gf[0] == '"' ? idf_unescape($gf) : $gf), "Trigger" => $C, "Statement" => $B[4],];
    }

    function triggers($R)
    {
        $I = [];
        $vi = trigger_options();
        foreach (get_rows("SELECT * FROM sqlite_master WHERE type = 'trigger' AND tbl_name = " . q($R)) as $J) {
            preg_match('~^CREATE\s+TRIGGER\s*(?:[^`"\s]+|`[^`]*`|"[^"]*")+\s*(' . implode("|", $vi["Timing"]) . ')\s*(.*)\s+ON\b~iU', $J["sql"], $B);
            $I[$J["name"]] = [$B[1], $B[2]];
        }

        return $I;
    }

    function trigger_options()
    {
        return
            ["Timing" => ["BEFORE", "AFTER", "INSTEAD OF"], "Event" => ["INSERT", "UPDATE", "UPDATE OF", "DELETE"], "Type" => ["FOR EACH ROW"],];
    }

    function begin()
    {
        return
            queries("BEGIN");
    }

    function last_id()
    {
        global $g;

        return $g->result("SELECT LAST_INSERT_ROWID()");
    }

    function explain($g, $G)
    {
        return $g->query("EXPLAIN QUERY PLAN $G");
    }

    function found_rows($S, $Z)
    {
    }

    function types()
    {
        return
            [];
    }

    function schemas()
    {
        return
            [];
    }

    function get_schema()
    {
        return "";
    }

    function set_schema($Yg)
    {
        return
            true;
    }

    function create_sql($R, $Ma, $Eh)
    {
        global $g;
        $I = $g->result("SELECT sql FROM sqlite_master WHERE type IN ('table', 'view') AND name = " . q($R));
        foreach (indexes($R) as $C => $v) {
            if ($C == '') {
                continue;
            }
            $I .= ";\n\n" . index_sql($R, $v['type'], $C, "(" . implode(", ", array_map('idf_escape', $v['columns'])) . ")");
        }

        return $I;
    }

    function truncate_sql($R)
    {
        return "DELETE FROM " . table($R);
    }

    function use_sql($j)
    {
    }

    function trigger_sql($R)
    {
        return
            implode(get_vals("SELECT sql || ';;\n' FROM sqlite_master WHERE type = 'trigger' AND tbl_name = " . q($R)));
    }

    function show_variables()
    {
        global $g;
        $I = [];
        foreach (["auto_vacuum", "cache_size", "count_changes", "default_cache_size", "empty_result_callbacks", "encoding", "foreign_keys", "full_column_names", "fullfsync", "journal_mode", "journal_size_limit", "legacy_file_format", "locking_mode", "page_size", "max_page_count", "read_uncommitted", "recursive_triggers", "reverse_unordered_selects", "secure_delete", "short_column_names", "synchronous", "temp_store", "temp_store_directory", "schema_version", "integrity_check", "quick_check"] as $y) {
            $I[$y] = $g->result("PRAGMA $y");
        }

        return $I;
    }

    function show_status()
    {
        $I = [];
        foreach (get_vals("PRAGMA compile_options") as $uf) {
            list($y, $X) = explode("=", $uf, 2);
            $I[$y] = $X;
        }

        return $I;
    }

    function convert_field($o)
    {
    }

    function unconvert_field($o, $I)
    {
        return $I;
    }

    function support($Qc)
    {
        return
            preg_match('~^(columns|database|drop_col|dump|indexes|move_col|sql|status|table|trigger|variables|view|view_trigger)$~', $Qc);
    }

    $x = "sqlite";
    $zi = ["integer" => 0, "real" => 0, "numeric" => 0, "text" => 0, "blob" => 0];
    $Dh = array_keys($zi);
    $Fi = [];
    $sf = ["=", "<", ">", "<=", ">=", "!=", "LIKE", "LIKE %%", "IN", "IS NULL", "NOT LIKE", "NOT IN", "IS NOT NULL", "SQL"];
    $ld = ["hex", "length", "lower", "round", "unixepoch", "upper"];
    $rd = ["avg", "count", "count distinct", "group_concat", "max", "min", "sum"];
    $nc = [[], ["integer|real|numeric" => "+/-", "text" => "||",]];
}
$fc["pgsql"] = "PostgreSQL";
if (isset($_GET["pgsql"])) {
    $eg = ["PgSQL", "PDO_PgSQL"];
    define("DRIVER", "pgsql");
    if (extension_loaded("pgsql")) {
        class Min_DB
        {
            public $extension = "PgSQL";

            public $_link;

            public $_result;

            public $_string;

            public $_database = true;

            public $server_info;

            public $affected_rows;

            public $error;

            public $timeout;

            public function _error($yc, $n)
            {
                if (ini_bool("html_errors")) {
                    $n = html_entity_decode(strip_tags($n));
                }
                $n = preg_replace('~^[^:]*: ~', '', $n);
                $this->error = $n;
            }

            public function connect($N, $V, $F)
            {
                global $b;
                $l = $b->database();
                set_error_handler([$this, '_error']);
                $this->_string = "host='" . str_replace(":", "' port='", addcslashes($N, "'\\")) . "' user='" . addcslashes($V, "'\\") . "' password='" . addcslashes($F, "'\\") . "'";
                $this->_link = @pg_connect("$this->_string dbname='" . ($l != "" ? addcslashes($l, "'\\") : "postgres") . "'", PGSQL_CONNECT_FORCE_NEW);
                if (!$this->_link && $l != "") {
                    $this->_database = false;
                    $this->_link = @pg_connect("$this->_string dbname='postgres'", PGSQL_CONNECT_FORCE_NEW);
                }
                restore_error_handler();
                if ($this->_link) {
                    $Ui = pg_version($this->_link);
                    $this->server_info = $Ui["server"];
                    pg_set_client_encoding($this->_link, "UTF8");
                }

                return (bool)$this->_link;
            }

            public function quote($Q)
            {
                return "'" . pg_escape_string($this->_link, $Q) . "'";
            }

            public function value($X, $o)
            {
                return ($o["type"] == "bytea" ? pg_unescape_bytea($X) : $X);
            }

            public function quoteBinary($Q)
            {
                return "'" . pg_escape_bytea($this->_link, $Q) . "'";
            }

            public function select_db($j)
            {
                global $b;
                if ($j == $b->database()) {
                    return $this->_database;
                }
                $I = @pg_connect("$this->_string dbname='" . addcslashes($j, "'\\") . "'", PGSQL_CONNECT_FORCE_NEW);
                if ($I) {
                    $this->_link = $I;
                }

                return $I;
            }

            public function close()
            {
                $this->_link = @pg_connect("$this->_string dbname='postgres'");
            }

            public function query($G, $_i = false)
            {
                $H = @pg_query($this->_link, $G);
                $this->error = "";
                if (!$H) {
                    $this->error = pg_last_error($this->_link);
                    $I = false;
                } elseif (!pg_num_fields($H)) {
                    $this->affected_rows = pg_affected_rows($H);
                    $I = true;
                } else {
                    $I = new
                Min_Result($H);
                }
                if ($this->timeout) {
                    $this->timeout = 0;
                    $this->query("RESET statement_timeout");
                }

                return $I;
            }

            public function multi_query($G)
            {
                return $this->_result = $this->query($G);
            }

            public function store_result()
            {
                return $this->_result;
            }

            public function next_result()
            {
                return
                    false;
            }

            public function result($G, $o = 0)
            {
                $H = $this->query($G);
                if (!$H || !$H->num_rows) {
                    return
                    false;
                }

                return
                    pg_fetch_result($H->_result, 0, $o);
            }

            public function warnings()
            {
                return
                    h(pg_last_notice($this->_link));
            }
        }

        class Min_Result
        {
            public $_result;

            public $_offset = 0;

            public $num_rows;

            public function __construct($H)
            {
                $this->_result = $H;
                $this->num_rows = pg_num_rows($H);
            }

            public function fetch_assoc()
            {
                return
                    pg_fetch_assoc($this->_result);
            }

            public function fetch_row()
            {
                return
                    pg_fetch_row($this->_result);
            }

            public function fetch_field()
            {
                $d = $this->_offset++;
                $I = new
                stdClass;
                if (function_exists('pg_field_table')) {
                    $I->orgtable = pg_field_table($this->_result, $d);
                }
                $I->name = pg_field_name($this->_result, $d);
                $I->orgname = $I->name;
                $I->type = pg_field_type($this->_result, $d);
                $I->charsetnr = ($I->type == "bytea" ? 63 : 0);

                return $I;
            }

            public function __destruct()
            {
                pg_free_result($this->_result);
            }
        }
    } elseif (extension_loaded("pdo_pgsql")) {
        class Min_DB extends
            Min_PDO
        {
            public $extension = "PDO_PgSQL";

            public $timeout;

            public function connect($N, $V, $F)
            {
                global $b;
                $l = $b->database();
                $Q = "pgsql:host='" . str_replace(":", "' port='", addcslashes($N, "'\\")) . "' options='-c client_encoding=utf8'";
                $this->dsn("$Q dbname='" . ($l != "" ? addcslashes($l, "'\\") : "postgres") . "'", $V, $F);

                return
                    true;
            }

            public function select_db($j)
            {
                global $b;

                return ($b->database() == $j);
            }

            public function quoteBinary($Vg)
            {
                return
                    q($Vg);
            }

            public function query($G, $_i = false)
            {
                $I = parent::query($G, $_i);
                if ($this->timeout) {
                    $this->timeout = 0;
                    parent::query("RESET statement_timeout");
                }

                return $I;
            }

            public function warnings()
            {
                return '';
            }

            public function close()
            {
            }
        }
    }

    class Min_Driver extends
        Min_SQL
    {
        public function insertUpdate($R, $K, $hg)
        {
            global $g;
            foreach ($K
                     as $O) {
                $Gi = [];
                $Z = [];
                foreach ($O
                         as $y => $X) {
                    $Gi[] = "$y = $X";
                    if (isset($hg[idf_unescape($y)])) {
                        $Z[] = "$y = $X";
                    }
                }
                if (!(($Z && queries("UPDATE " . table($R) . " SET " . implode(", ", $Gi) . " WHERE " . implode(" AND ", $Z)) && $g->affected_rows) || queries("INSERT INTO " . table($R) . " (" . implode(", ", array_keys($O)) . ") VALUES (" . implode(", ", $O) . ")"))) {
                    return
                    false;
                }
            }

            return
                true;
        }

        public function slowQuery($G, $bi)
        {
            $this->_conn->query("SET statement_timeout = " . (1000 * $bi));
            $this->_conn->timeout = 1000 * $bi;

            return $G;
        }

        public function convertSearch($u, $X, $o)
        {
            return (preg_match('~char|text' . (!preg_match('~LIKE~', $X["op"]) ? '|date|time(stamp)?|boolean|uuid|' . number_type() : '') . '~', $o["type"]) ? $u : "CAST($u AS text)");
        }

        public function quoteBinary($Vg)
        {
            return $this->_conn->quoteBinary($Vg);
        }

        public function warnings()
        {
            return $this->_conn->warnings();
        }

        public function tableHelp($C)
        {
            $we = ["information_schema" => "infoschema", "pg_catalog" => "catalog",];
            $_ = $we[$_GET["ns"]];
            if ($_) {
                return "$_-" . str_replace("_", "-", $C) . ".html";
            }
        }
    }

    function idf_escape($u)
    {
        return '"' . str_replace('"', '""', $u) . '"';
    }

    function table($u)
    {
        return
            idf_escape($u);
    }

    function connect()
    {
        global $b, $zi, $Dh;
        $g = new
        Min_DB;
        $Hb = $b->credentials();
        if ($g->connect($Hb[0], $Hb[1], $Hb[2])) {
            if (min_version(9, 0, $g)) {
                $g->query("SET application_name = 'Adminer'");
                if (min_version(9.2, 0, $g)) {
                    $Dh[lang(25)][] = "json";
                    $zi["json"] = 4294967295;
                    if (min_version(9.4, 0, $g)) {
                        $Dh[lang(25)][] = "jsonb";
                        $zi["jsonb"] = 4294967295;
                    }
                }
            }

            return $g;
        }

        return $g->error;
    }

    function get_databases()
    {
        return
            get_vals("SELECT datname FROM pg_database WHERE has_database_privilege(datname, 'CONNECT') ORDER BY datname");
    }

    function limit($G, $Z, $z, $D = 0, $M = " ")
    {
        return " $G$Z" . ($z !== null ? $M . "LIMIT $z" . ($D ? " OFFSET $D" : "") : "");
    }

    function limit1($R, $G, $Z, $M = "\n")
    {
        return (preg_match('~^INTO~', $G) ? limit($G, $Z, 1, 0, $M) : " $G" . (is_view(table_status1($R)) ? $Z : " WHERE ctid = (SELECT ctid FROM " . table($R) . $Z . $M . "LIMIT 1)"));
    }

    function db_collation($l, $qb)
    {
        global $g;

        return $g->result("SHOW LC_COLLATE");
    }

    function engines()
    {
        return
            [];
    }

    function logged_user()
    {
        global $g;

        return $g->result("SELECT user");
    }

    function tables_list()
    {
        $G = "SELECT table_name, table_type FROM information_schema.tables WHERE table_schema = current_schema()";
        if (support('materializedview')) {
            $G .= "
UNION ALL
SELECT matviewname, 'MATERIALIZED VIEW'
FROM pg_matviews
WHERE schemaname = current_schema()";
        }
        $G .= "
ORDER BY 1";

        return
            get_key_vals($G);
    }

    function count_tables($k)
    {
        return
            [];
    }

    function table_status($C = "")
    {
        $I = [];
        foreach (get_rows("SELECT c.relname AS \"Name\", CASE c.relkind WHEN 'r' THEN 'table' WHEN 'm' THEN 'materialized view' ELSE 'view' END AS \"Engine\", pg_relation_size(c.oid) AS \"Data_length\", pg_total_relation_size(c.oid) - pg_relation_size(c.oid) AS \"Index_length\", obj_description(c.oid, 'pg_class') AS \"Comment\", CASE WHEN c.relhasoids THEN 'oid' ELSE '' END AS \"Oid\", c.reltuples as \"Rows\", n.nspname
FROM pg_class c
JOIN pg_namespace n ON(n.nspname = current_schema() AND n.oid = c.relnamespace)
WHERE relkind IN ('r', 'm', 'v', 'f')
" . ($C != "" ? "AND relname = " . q($C) : "ORDER BY relname")) as $J) {
            $I[$J["Name"]] = $J;
        }

        return ($C != "" ? $I[$C] : $I);
    }

    function is_view($S)
    {
        return
            in_array($S["Engine"], ["view", "materialized view"]);
    }

    function fk_support($S)
    {
        return
            true;
    }

    function fields($R)
    {
        $I = [];
        $Da = ['timestamp without time zone' => 'timestamp', 'timestamp with time zone' => 'timestamptz',];
        foreach (get_rows("SELECT a.attname AS field, format_type(a.atttypid, a.atttypmod) AS full_type, d.adsrc AS default, a.attnotnull::int, col_description(c.oid, a.attnum) AS comment
FROM pg_class c
JOIN pg_namespace n ON c.relnamespace = n.oid
JOIN pg_attribute a ON c.oid = a.attrelid
LEFT JOIN pg_attrdef d ON c.oid = d.adrelid AND a.attnum = d.adnum
WHERE c.relname = " . q($R) . "
AND n.nspname = current_schema()
AND NOT a.attisdropped
AND a.attnum > 0
ORDER BY a.attnum") as $J) {
            preg_match('~([^([]+)(\((.*)\))?([a-z ]+)?((\[[0-9]*])*)$~', $J["full_type"], $B);
            list(, $U, $te, $J["length"], $xa, $Ga) = $B;
            $J["length"] .= $Ga;
            $eb = $U . $xa;
            if (isset($Da[$eb])) {
                $J["type"] = $Da[$eb];
                $J["full_type"] = $J["type"] . $te . $Ga;
            } else {
                $J["type"] = $U;
                $J["full_type"] = $J["type"] . $te . $xa . $Ga;
            }
            $J["null"] = !$J["attnotnull"];
            $J["auto_increment"] = preg_match('~^nextval\(~i', $J["default"]);
            $J["privileges"] = ["insert" => 1, "select" => 1, "update" => 1];
            if (preg_match('~(.+)::[^)]+(.*)~', $J["default"], $B)) {
                $J["default"] = ($B[1] == "NULL" ? null : (($B[1][0] == "'" ? idf_unescape($B[1]) : $B[1]) . $B[2]));
            }
            $I[$J["field"]] = $J;
        }

        return $I;
    }

    function indexes($R, $h = null)
    {
        global $g;
        if (!is_object($h)) {
            $h = $g;
        }
        $I = [];
        $Mh = $h->result("SELECT oid FROM pg_class WHERE relnamespace = (SELECT oid FROM pg_namespace WHERE nspname = current_schema()) AND relname = " . q($R));
        $e = get_key_vals("SELECT attnum, attname FROM pg_attribute WHERE attrelid = $Mh AND attnum > 0", $h);
        foreach (get_rows("SELECT relname, indisunique::int, indisprimary::int, indkey, indoption , (indpred IS NOT NULL)::int as indispartial FROM pg_index i, pg_class ci WHERE i.indrelid = $Mh AND ci.oid = i.indexrelid", $h) as $J) {
            $Fg = $J["relname"];
            $I[$Fg]["type"] = ($J["indispartial"] ? "INDEX" : ($J["indisprimary"] ? "PRIMARY" : ($J["indisunique"] ? "UNIQUE" : "INDEX")));
            $I[$Fg]["columns"] = [];
            foreach (explode(" ", $J["indkey"]) as $Md) {
                $I[$Fg]["columns"][] = $e[$Md];
            }
            $I[$Fg]["descs"] = [];
            foreach (explode(" ", $J["indoption"]) as $Nd) {
                $I[$Fg]["descs"][] = ($Nd & 1 ? '1' : null);
            }
            $I[$Fg]["lengths"] = [];
        }

        return $I;
    }

    function foreign_keys($R)
    {
        global $nf;
        $I = [];
        foreach (get_rows("SELECT conname, condeferrable::int AS deferrable, pg_get_constraintdef(oid) AS definition
FROM pg_constraint
WHERE conrelid = (SELECT pc.oid FROM pg_class AS pc INNER JOIN pg_namespace AS pn ON (pn.oid = pc.relnamespace) WHERE pc.relname = " . q($R) . " AND pn.nspname = current_schema())
AND contype = 'f'::char
ORDER BY conkey, conname") as $J) {
            if (preg_match('~FOREIGN KEY\s*\((.+)\)\s*REFERENCES (.+)\((.+)\)(.*)$~iA', $J['definition'], $B)) {
                $J['source'] = array_map('trim', explode(',', $B[1]));
                if (preg_match('~^(("([^"]|"")+"|[^"]+)\.)?"?("([^"]|"")+"|[^"]+)$~', $B[2], $Be)) {
                    $J['ns'] = str_replace('""', '"', preg_replace('~^"(.+)"$~', '\1', $Be[2]));
                    $J['table'] = str_replace('""', '"', preg_replace('~^"(.+)"$~', '\1', $Be[4]));
                }
                $J['target'] = array_map('trim', explode(',', $B[3]));
                $J['on_delete'] = (preg_match("~ON DELETE ($nf)~", $B[4], $Be) ? $Be[1] : 'NO ACTION');
                $J['on_update'] = (preg_match("~ON UPDATE ($nf)~", $B[4], $Be) ? $Be[1] : 'NO ACTION');
                $I[$J['conname']] = $J;
            }
        }

        return $I;
    }

    function view($C)
    {
        global $g;

        return
            ["select" => trim($g->result("SELECT view_definition
FROM information_schema.views
WHERE table_schema = current_schema() AND table_name = " . q($C)))];
    }

    function collations()
    {
        return
            [];
    }

    function information_schema($l)
    {
        return ($l == "information_schema");
    }

    function error()
    {
        global $g;
        $I = h($g->error);
        if (preg_match('~^(.*\n)?([^\n]*)\n( *)\^(\n.*)?$~s', $I, $B)) {
            $I = $B[1] . preg_replace('~((?:[^&]|&[^;]*;){' . strlen($B[3]) . '})(.*)~', '\1<b>\2</b>', $B[2]) . $B[4];
        }

        return
            nl_br($I);
    }

    function create_database($l, $pb)
    {
        return
            queries("CREATE DATABASE " . idf_escape($l) . ($pb ? " ENCODING " . idf_escape($pb) : ""));
    }

    function drop_databases($k)
    {
        global $g;
        $g->close();

        return
            apply_queries("DROP DATABASE", $k, 'idf_escape');
    }

    function rename_database($C, $pb)
    {
        return
            queries("ALTER DATABASE " . idf_escape(DB) . " RENAME TO " . idf_escape($C));
    }

    function auto_increment()
    {
        return "";
    }

    function alter_table($R, $C, $p, $dd, $vb, $vc, $pb, $Ma, $Rf)
    {
        $c = [];
        $sg = [];
        foreach ($p
                 as $o) {
            $d = idf_escape($o[0]);
            $X = $o[1];
            if (!$X) {
                $c[] = "DROP $d";
            } else {
                $Qi = $X[5];
                unset($X[5]);
                if (isset($X[6]) && $o[0] == "") {
                    $X[1] = ($X[1] == "bigint" ? " big" : " ") . "serial";
                }
                if ($o[0] == "") {
                    $c[] = ($R != "" ? "ADD " : "  ") . implode($X);
                } else {
                    if ($d != $X[0]) {
                        $sg[] = "ALTER TABLE " . table($R) . " RENAME $d TO $X[0]";
                    }
                    $c[] = "ALTER $d TYPE$X[1]";
                    if (!$X[6]) {
                        $c[] = "ALTER $d " . ($X[3] ? "SET$X[3]" : "DROP DEFAULT");
                        $c[] = "ALTER $d " . ($X[2] == " NULL" ? "DROP NOT" : "SET") . $X[2];
                    }
                }
                if ($o[0] != "" || $Qi != "") {
                    $sg[] = "COMMENT ON COLUMN " . table($R) . ".$X[0] IS " . ($Qi != "" ? substr($Qi, 9) : "''");
                }
            }
        }
        $c = array_merge($c, $dd);
        if ($R == "") {
            array_unshift($sg, "CREATE TABLE " . table($C) . " (\n" . implode(",\n", $c) . "\n)");
        } elseif ($c) {
            array_unshift($sg, "ALTER TABLE " . table($R) . "\n" . implode(",\n", $c));
        }
        if ($R != "" && $R != $C) {
            $sg[] = "ALTER TABLE " . table($R) . " RENAME TO " . table($C);
        }
        if ($R != "" || $vb != "") {
            $sg[] = "COMMENT ON TABLE " . table($C) . " IS " . q($vb);
        }
        if ($Ma != "") {
        }
        foreach ($sg
                 as $G) {
            if (!queries($G)) {
                return
                false;
            }
        }

        return
            true;
    }

    function alter_indexes($R, $c)
    {
        $i = [];
        $gc = [];
        $sg = [];
        foreach ($c
                 as $X) {
            if ($X[0] != "INDEX") {
                $i[] = ($X[2] == "DROP" ? "\nDROP CONSTRAINT " . idf_escape($X[1]) : "\nADD" . ($X[1] != "" ? " CONSTRAINT " . idf_escape($X[1]) : "") . " $X[0] " . ($X[0] == "PRIMARY" ? "KEY " : "") . "(" . implode(", ", $X[2]) . ")");
            } elseif ($X[2] == "DROP") {
                $gc[] = idf_escape($X[1]);
            } else {
                $sg[] = "CREATE INDEX " . idf_escape($X[1] != "" ? $X[1] : uniqid($R . "_")) . " ON " . table($R) . " (" . implode(", ", $X[2]) . ")";
            }
        }
        if ($i) {
            array_unshift($sg, "ALTER TABLE " . table($R) . implode(",", $i));
        }
        if ($gc) {
            array_unshift($sg, "DROP INDEX " . implode(", ", $gc));
        }
        foreach ($sg
                 as $G) {
            if (!queries($G)) {
                return
                false;
            }
        }

        return
            true;
    }

    function truncate_tables($T)
    {
        return
            queries("TRUNCATE " . implode(", ", array_map('table', $T)));

        return
            true;
    }

    function drop_views($Wi)
    {
        return
            drop_tables($Wi);
    }

    function drop_tables($T)
    {
        foreach ($T
                 as $R) {
            $P = table_status($R);
            if (!queries("DROP " . strtoupper($P["Engine"]) . " " . table($R))) {
                return
                false;
            }
        }

        return
            true;
    }

    function move_tables($T, $Wi, $Th)
    {
        foreach (array_merge($T, $Wi) as $R) {
            $P = table_status($R);
            if (!queries("ALTER " . strtoupper($P["Engine"]) . " " . table($R) . " SET SCHEMA " . idf_escape($Th))) {
                return
                false;
            }
        }

        return
            true;
    }

    function trigger($C, $R = null)
    {
        if ($C == "") {
            return
            ["Statement" => "EXECUTE PROCEDURE ()"];
        }
        if ($R === null) {
            $R = $_GET['trigger'];
        }
        $K = get_rows('SELECT t.trigger_name AS "Trigger", t.action_timing AS "Timing", (SELECT STRING_AGG(event_manipulation, \' OR \') FROM information_schema.triggers WHERE event_object_table = t.event_object_table AND trigger_name = t.trigger_name ) AS "Events", t.event_manipulation AS "Event", \'FOR EACH \' || t.action_orientation AS "Type", t.action_statement AS "Statement" FROM information_schema.triggers t WHERE t.event_object_table = ' . q($R) . ' AND t.trigger_name = ' . q($C));

        return
            reset($K);
    }

    function triggers($R)
    {
        $I = [];
        foreach (get_rows("SELECT * FROM information_schema.triggers WHERE event_object_table = " . q($R)) as $J) {
            $I[$J["trigger_name"]] = [$J["action_timing"], $J["event_manipulation"]];
        }

        return $I;
    }

    function trigger_options()
    {
        return
            ["Timing" => ["BEFORE", "AFTER"], "Event" => ["INSERT", "UPDATE", "DELETE"], "Type" => ["FOR EACH ROW", "FOR EACH STATEMENT"],];
    }

    function routine($C, $U)
    {
        $K = get_rows('SELECT routine_definition AS definition, LOWER(external_language) AS language, *
FROM information_schema.routines
WHERE routine_schema = current_schema() AND specific_name = ' . q($C));
        $I = $K[0];
        $I["returns"] = ["type" => $I["type_udt_name"]];
        $I["fields"] = get_rows('SELECT parameter_name AS field, data_type AS type, character_maximum_length AS length, parameter_mode AS inout
FROM information_schema.parameters
WHERE specific_schema = current_schema() AND specific_name = ' . q($C) . '
ORDER BY ordinal_position');

        return $I;
    }

    function routines()
    {
        return
            get_rows('SELECT specific_name AS "SPECIFIC_NAME", routine_type AS "ROUTINE_TYPE", routine_name AS "ROUTINE_NAME", type_udt_name AS "DTD_IDENTIFIER"
FROM information_schema.routines
WHERE routine_schema = current_schema()
ORDER BY SPECIFIC_NAME');
    }

    function routine_languages()
    {
        return
            get_vals("SELECT LOWER(lanname) FROM pg_catalog.pg_language");
    }

    function routine_id($C, $J)
    {
        $I = [];
        foreach ($J["fields"] as $o) {
            $I[] = $o["type"];
        }

        return
            idf_escape($C) . "(" . implode(", ", $I) . ")";
    }

    function last_id()
    {
        return
            0;
    }

    function explain($g, $G)
    {
        return $g->query("EXPLAIN $G");
    }

    function found_rows($S, $Z)
    {
        global $g;
        if (preg_match("~ rows=([0-9]+)~", $g->result("EXPLAIN SELECT * FROM " . idf_escape($S["Name"]) . ($Z ? " WHERE " . implode(" AND ", $Z) : "")), $Eg)) {
            return $Eg[1];
        }

        return
            false;
    }

    function types()
    {
        return
            get_vals("SELECT typname
FROM pg_type
WHERE typnamespace = (SELECT oid FROM pg_namespace WHERE nspname = current_schema())
AND typtype IN ('b','d','e')
AND typelem = 0");
    }

    function schemas()
    {
        return
            get_vals("SELECT nspname FROM pg_namespace ORDER BY nspname");
    }

    function get_schema()
    {
        global $g;

        return $g->result("SELECT current_schema()");
    }

    function set_schema($Xg)
    {
        global $g, $zi, $Dh;
        $I = $g->query("SET search_path TO " . idf_escape($Xg));
        foreach (types() as $U) {
            if (!isset($zi[$U])) {
                $zi[$U] = 0;
                $Dh[lang(26)][] = $U;
            }
        }

        return $I;
    }

    function create_sql($R, $Ma, $Eh)
    {
        global $g;
        $I = '';
        $Ng = [];
        $hh = [];
        $P = table_status($R);
        $p = fields($R);
        $w = indexes($R);
        ksort($w);
        $ad = foreign_keys($R);
        ksort($ad);
        if (!$P || empty($p)) {
            return
            false;
        }
        $I = "CREATE TABLE " . idf_escape($P['nspname']) . "." . idf_escape($P['Name']) . " (\n    ";
        foreach ($p
                 as $Sc => $o) {
            $Of = idf_escape($o['field']) . ' ' . $o['full_type'] . default_value($o) . ($o['attnotnull'] ? " NOT NULL" : "");
            $Ng[] = $Of;
            if (preg_match('~nextval\(\'([^\']+)\'\)~', $o['default'], $Ce)) {
                $gh = $Ce[1];
                $uh = reset(get_rows(min_version(10) ? "SELECT *, cache_size AS cache_value FROM pg_sequences WHERE schemaname = current_schema() AND sequencename = " . q($gh) : "SELECT * FROM $gh"));
                $hh[] = ($Eh == "DROP+CREATE" ? "DROP SEQUENCE IF EXISTS $gh;\n" : "") . "CREATE SEQUENCE $gh INCREMENT $uh[increment_by] MINVALUE $uh[min_value] MAXVALUE $uh[max_value] START " . ($Ma ? $uh['last_value'] : 1) . " CACHE $uh[cache_value];";
            }
        }
        if (!empty($hh)) {
            $I = implode("\n\n", $hh) . "\n\n$I";
        }
        foreach ($w
                 as $Hd => $v) {
            switch ($v['type']) {
                case'UNIQUE':
                    $Ng[] = "CONSTRAINT " . idf_escape($Hd) . " UNIQUE (" . implode(', ', array_map('idf_escape', $v['columns'])) . ")";
                    break;
                case'PRIMARY':
                    $Ng[] = "CONSTRAINT " . idf_escape($Hd) . " PRIMARY KEY (" . implode(', ', array_map('idf_escape', $v['columns'])) . ")";
                    break;
            }
        }
        foreach ($ad
                 as $Zc => $Yc) {
            $Ng[] = "CONSTRAINT " . idf_escape($Zc) . " $Yc[definition] " . ($Yc['deferrable'] ? 'DEFERRABLE' : 'NOT DEFERRABLE');
        }
        $I .= implode(",\n    ", $Ng) . "\n) WITH (oids = " . ($P['Oid'] ? 'true' : 'false') . ");";
        foreach ($w
                 as $Hd => $v) {
            if ($v['type'] == 'INDEX') {
                $I .= "\n\nCREATE INDEX " . idf_escape($Hd) . " ON " . idf_escape($P['nspname']) . "." . idf_escape($P['Name']) . " USING btree (" . implode(', ', array_map('idf_escape', $v['columns'])) . ");";
            }
        }
        if ($P['Comment']) {
            $I .= "\n\nCOMMENT ON TABLE " . idf_escape($P['nspname']) . "." . idf_escape($P['Name']) . " IS " . q($P['Comment']) . ";";
        }
        foreach ($p
                 as $Sc => $o) {
            if ($o['comment']) {
                $I .= "\n\nCOMMENT ON COLUMN " . idf_escape($P['nspname']) . "." . idf_escape($P['Name']) . "." . idf_escape($Sc) . " IS " . q($o['comment']) . ";";
            }
        }

        return
            rtrim($I, ';');
    }

    function truncate_sql($R)
    {
        return "TRUNCATE " . table($R);
    }

    function trigger_sql($R)
    {
        $P = table_status($R);
        $I = "";
        foreach (triggers($R) as $si => $ri) {
            $ti = trigger($si, $P['Name']);
            $I .= "\nCREATE TRIGGER " . idf_escape($ti['Trigger']) . " $ti[Timing] $ti[Events] ON " . idf_escape($P["nspname"]) . "." . idf_escape($P['Name']) . " $ti[Type] $ti[Statement];;\n";
        }

        return $I;
    }

    function use_sql($j)
    {
        return "\connect " . idf_escape($j);
    }

    function show_variables()
    {
        return
            get_key_vals("SHOW ALL");
    }

    function process_list()
    {
        return
            get_rows("SELECT * FROM pg_stat_activity ORDER BY " . (min_version(9.2) ? "pid" : "procpid"));
    }

    function show_status()
    {
    }

    function convert_field($o)
    {
    }

    function unconvert_field($o, $I)
    {
        return $I;
    }

    function support($Qc)
    {
        return
            preg_match('~^(database|table|columns|sql|indexes|comment|view|' . (min_version(9.3) ? 'materializedview|' : '') . 'scheme|routine|processlist|sequence|trigger|type|variables|drop_col|kill|dump)$~', $Qc);
    }

    function kill_process($X)
    {
        return
            queries("SELECT pg_terminate_backend(" . number($X) . ")");
    }

    function connection_id()
    {
        return "SELECT pg_backend_pid()";
    }

    function max_connections()
    {
        global $g;

        return $g->result("SHOW max_connections");
    }

    $x = "pgsql";
    $zi = [];
    $Dh = [];
    foreach ([lang(27) => ["smallint" => 5, "integer" => 10, "bigint" => 19, "boolean" => 1, "numeric" => 0, "real" => 7, "double precision" => 16, "money" => 20], lang(28) => ["date" => 13, "time" => 17, "timestamp" => 20, "timestamptz" => 21, "interval" => 0], lang(25) => ["character" => 0, "character varying" => 0, "text" => 0, "tsquery" => 0, "tsvector" => 0, "uuid" => 0, "xml" => 0], lang(29) => ["bit" => 0, "bit varying" => 0, "bytea" => 0], lang(30) => ["cidr" => 43, "inet" => 43, "macaddr" => 17, "txid_snapshot" => 0], lang(31) => ["box" => 0, "circle" => 0, "line" => 0, "lseg" => 0, "path" => 0, "point" => 0, "polygon" => 0],] as $y => $X) {
        $zi += $X;
        $Dh[$y] = array_keys($X);
    }
    $Fi = [];
    $sf = ["=", "<", ">", "<=", ">=", "!=", "~", "!~", "LIKE", "LIKE %%", "ILIKE", "ILIKE %%", "IN", "IS NULL", "NOT LIKE", "NOT IN", "IS NOT NULL"];
    $ld = ["char_length", "lower", "round", "to_hex", "to_timestamp", "upper"];
    $rd = ["avg", "count", "count distinct", "max", "min", "sum"];
    $nc = [["char" => "md5", "date|time" => "now",], [number_type() => "+/-", "date|time" => "+ interval/- interval", "char|text" => "||",]];
}
$fc["oracle"] = "Oracle (beta)";
if (isset($_GET["oracle"])) {
    $eg = ["OCI8", "PDO_OCI"];
    define("DRIVER", "oracle");
    if (extension_loaded("oci8")) {
        class Min_DB
        {
            public $extension = "oci8";

            public $_link;

            public $_result;

            public $server_info;

            public $affected_rows;

            public $errno;

            public $error;

            public function _error($yc, $n)
            {
                if (ini_bool("html_errors")) {
                    $n = html_entity_decode(strip_tags($n));
                }
                $n = preg_replace('~^[^:]*: ~', '', $n);
                $this->error = $n;
            }

            public function connect($N, $V, $F)
            {
                $this->_link = @oci_new_connect($V, $F, $N, "AL32UTF8");
                if ($this->_link) {
                    $this->server_info = oci_server_version($this->_link);

                    return
                        true;
                }
                $n = oci_error();
                $this->error = $n["message"];

                return
                    false;
            }

            public function quote($Q)
            {
                return "'" . str_replace("'", "''", $Q) . "'";
            }

            public function select_db($j)
            {
                return
                    true;
            }

            public function query($G, $_i = false)
            {
                $H = oci_parse($this->_link, $G);
                $this->error = "";
                if (!$H) {
                    $n = oci_error($this->_link);
                    $this->errno = $n["code"];
                    $this->error = $n["message"];

                    return
                        false;
                }
                set_error_handler([$this, '_error']);
                $I = @oci_execute($H);
                restore_error_handler();
                if ($I) {
                    if (oci_num_fields($H)) {
                        return
                        new
                        Min_Result($H);
                    }
                    $this->affected_rows = oci_num_rows($H);
                }

                return $I;
            }

            public function multi_query($G)
            {
                return $this->_result = $this->query($G);
            }

            public function store_result()
            {
                return $this->_result;
            }

            public function next_result()
            {
                return
                    false;
            }

            public function result($G, $o = 1)
            {
                $H = $this->query($G);
                if (!is_object($H) || !oci_fetch($H->_result)) {
                    return
                    false;
                }

                return
                    oci_result($H->_result, $o);
            }
        }

        class Min_Result
        {
            public $_result;

            public $_offset = 1;

            public $num_rows;

            public function __construct($H)
            {
                $this->_result = $H;
            }

            public function _convert($J)
            {
                foreach ((array)$J
                         as $y => $X) {
                    if (is_a($X, 'OCI-Lob')) {
                        $J[$y] = $X->load();
                    }
                }

                return $J;
            }

            public function fetch_assoc()
            {
                return $this->_convert(oci_fetch_assoc($this->_result));
            }

            public function fetch_row()
            {
                return $this->_convert(oci_fetch_row($this->_result));
            }

            public function fetch_field()
            {
                $d = $this->_offset++;
                $I = new
                stdClass;
                $I->name = oci_field_name($this->_result, $d);
                $I->orgname = $I->name;
                $I->type = oci_field_type($this->_result, $d);
                $I->charsetnr = (preg_match("~raw|blob|bfile~", $I->type) ? 63 : 0);

                return $I;
            }

            public function __destruct()
            {
                oci_free_statement($this->_result);
            }
        }
    } elseif (extension_loaded("pdo_oci")) {
        class Min_DB extends
            Min_PDO
        {
            public $extension = "PDO_OCI";

            public function connect($N, $V, $F)
            {
                $this->dsn("oci:dbname=//$N;charset=AL32UTF8", $V, $F);

                return
                    true;
            }

            public function select_db($j)
            {
                return
                    true;
            }
        }
    }

    class Min_Driver extends
        Min_SQL
    {
        public function begin()
        {
            return
                true;
        }
    }

    function idf_escape($u)
    {
        return '"' . str_replace('"', '""', $u) . '"';
    }

    function table($u)
    {
        return
            idf_escape($u);
    }

    function connect()
    {
        global $b;
        $g = new
        Min_DB;
        $Hb = $b->credentials();
        if ($g->connect($Hb[0], $Hb[1], $Hb[2])) {
            return $g;
        }

        return $g->error;
    }

    function get_databases()
    {
        return
            get_vals("SELECT tablespace_name FROM user_tablespaces");
    }

    function limit($G, $Z, $z, $D = 0, $M = " ")
    {
        return ($D ? " * FROM (SELECT t.*, rownum AS rnum FROM (SELECT $G$Z) t WHERE rownum <= " . ($z + $D) . ") WHERE rnum > $D" : ($z !== null ? " * FROM (SELECT $G$Z) WHERE rownum <= " . ($z + $D) : " $G$Z"));
    }

    function limit1($R, $G, $Z, $M = "\n")
    {
        return " $G$Z";
    }

    function db_collation($l, $qb)
    {
        global $g;

        return $g->result("SELECT value FROM nls_database_parameters WHERE parameter = 'NLS_CHARACTERSET'");
    }

    function engines()
    {
        return
            [];
    }

    function logged_user()
    {
        global $g;

        return $g->result("SELECT USER FROM DUAL");
    }

    function tables_list()
    {
        return
            get_key_vals("SELECT table_name, 'table' FROM all_tables WHERE tablespace_name = " . q(DB) . "
UNION SELECT view_name, 'view' FROM user_views
ORDER BY 1");
    }

    function count_tables($k)
    {
        return
            [];
    }

    function table_status($C = "")
    {
        $I = [];
        $Zg = q($C);
        foreach (get_rows('SELECT table_name "Name", \'table\' "Engine", avg_row_len * num_rows "Data_length", num_rows "Rows" FROM all_tables WHERE tablespace_name = ' . q(DB) . ($C != "" ? " AND table_name = $Zg" : "") . "
UNION SELECT view_name, 'view', 0, 0 FROM user_views" . ($C != "" ? " WHERE view_name = $Zg" : "") . "
ORDER BY 1") as $J) {
            if ($C != "") {
                return $J;
            }
            $I[$J["Name"]] = $J;
        }

        return $I;
    }

    function is_view($S)
    {
        return $S["Engine"] == "view";
    }

    function fk_support($S)
    {
        return
            true;
    }

    function fields($R)
    {
        $I = [];
        foreach (get_rows("SELECT * FROM all_tab_columns WHERE table_name = " . q($R) . " ORDER BY column_id") as $J) {
            $U = $J["DATA_TYPE"];
            $te = "$J[DATA_PRECISION],$J[DATA_SCALE]";
            if ($te == ",") {
                $te = $J["DATA_LENGTH"];
            }
            $I[$J["COLUMN_NAME"]] = ["field" => $J["COLUMN_NAME"], "full_type" => $U . ($te ? "($te)" : ""), "type" => strtolower($U), "length" => $te, "default" => $J["DATA_DEFAULT"], "null" => ($J["NULLABLE"] == "Y"), "privileges" => ["insert" => 1, "select" => 1, "update" => 1],];
        }

        return $I;
    }

    function indexes($R, $h = null)
    {
        $I = [];
        foreach (get_rows("SELECT uic.*, uc.constraint_type
FROM user_ind_columns uic
LEFT JOIN user_constraints uc ON uic.index_name = uc.constraint_name AND uic.table_name = uc.table_name
WHERE uic.table_name = " . q($R) . "
ORDER BY uc.constraint_type, uic.column_position", $h) as $J) {
            $Hd = $J["INDEX_NAME"];
            $I[$Hd]["type"] = ($J["CONSTRAINT_TYPE"] == "P" ? "PRIMARY" : ($J["CONSTRAINT_TYPE"] == "U" ? "UNIQUE" : "INDEX"));
            $I[$Hd]["columns"][] = $J["COLUMN_NAME"];
            $I[$Hd]["lengths"][] = ($J["CHAR_LENGTH"] && $J["CHAR_LENGTH"] != $J["COLUMN_LENGTH"] ? $J["CHAR_LENGTH"] : null);
            $I[$Hd]["descs"][] = ($J["DESCEND"] ? '1' : null);
        }

        return $I;
    }

    function view($C)
    {
        $K = get_rows('SELECT text "select" FROM user_views WHERE view_name = ' . q($C));

        return
            reset($K);
    }

    function collations()
    {
        return
            [];
    }

    function information_schema($l)
    {
        return
            false;
    }

    function error()
    {
        global $g;

        return
            h($g->error);
    }

    function explain($g, $G)
    {
        $g->query("EXPLAIN PLAN FOR $G");

        return $g->query("SELECT * FROM plan_table");
    }

    function found_rows($S, $Z)
    {
    }

    function alter_table($R, $C, $p, $dd, $vb, $vc, $pb, $Ma, $Rf)
    {
        $c = $gc = [];
        foreach ($p
                 as $o) {
            $X = $o[1];
            if ($X && $o[0] != "" && idf_escape($o[0]) != $X[0]) {
                queries("ALTER TABLE " . table($R) . " RENAME COLUMN " . idf_escape($o[0]) . " TO $X[0]");
            }
            if ($X) {
                $c[] = ($R != "" ? ($o[0] != "" ? "MODIFY (" : "ADD (") : "  ") . implode($X) . ($R != "" ? ")" : "");
            } else {
                $gc[] = idf_escape($o[0]);
            }
        }
        if ($R == "") {
            return
            queries("CREATE TABLE " . table($C) . " (\n" . implode(",\n", $c) . "\n)");
        }

        return (!$c || queries("ALTER TABLE " . table($R) . "\n" . implode("\n", $c))) && (!$gc || queries("ALTER TABLE " . table($R) . " DROP (" . implode(", ", $gc) . ")")) && ($R == $C || queries("ALTER TABLE " . table($R) . " RENAME TO " . table($C)));
    }

    function foreign_keys($R)
    {
        $I = [];
        $G = "SELECT c_list.CONSTRAINT_NAME as NAME,
c_src.COLUMN_NAME as SRC_COLUMN,
c_dest.OWNER as DEST_DB,
c_dest.TABLE_NAME as DEST_TABLE,
c_dest.COLUMN_NAME as DEST_COLUMN,
c_list.DELETE_RULE as ON_DELETE
FROM ALL_CONSTRAINTS c_list, ALL_CONS_COLUMNS c_src, ALL_CONS_COLUMNS c_dest
WHERE c_list.CONSTRAINT_NAME = c_src.CONSTRAINT_NAME
AND c_list.R_CONSTRAINT_NAME = c_dest.CONSTRAINT_NAME
AND c_list.CONSTRAINT_TYPE = 'R'
AND c_src.TABLE_NAME = " . q($R);
        foreach (get_rows($G) as $J) {
            $I[$J['NAME']] = ["db" => $J['DEST_DB'], "table" => $J['DEST_TABLE'], "source" => [$J['SRC_COLUMN']], "target" => [$J['DEST_COLUMN']], "on_delete" => $J['ON_DELETE'], "on_update" => null,];
        }

        return $I;
    }

    function truncate_tables($T)
    {
        return
            apply_queries("TRUNCATE TABLE", $T);
    }

    function drop_views($Wi)
    {
        return
            apply_queries("DROP VIEW", $Wi);
    }

    function drop_tables($T)
    {
        return
            apply_queries("DROP TABLE", $T);
    }

    function last_id()
    {
        return
            0;
    }

    function schemas()
    {
        return
            get_vals("SELECT DISTINCT owner FROM dba_segments WHERE owner IN (SELECT username FROM dba_users WHERE default_tablespace NOT IN ('SYSTEM','SYSAUX'))");
    }

    function get_schema()
    {
        global $g;

        return $g->result("SELECT sys_context('USERENV', 'SESSION_USER') FROM dual");
    }

    function set_schema($Yg)
    {
        global $g;

        return $g->query("ALTER SESSION SET CURRENT_SCHEMA = " . idf_escape($Yg));
    }

    function show_variables()
    {
        return
            get_key_vals('SELECT name, display_value FROM v$parameter');
    }

    function process_list()
    {
        return
            get_rows('SELECT sess.process AS "process", sess.username AS "user", sess.schemaname AS "schema", sess.status AS "status", sess.wait_class AS "wait_class", sess.seconds_in_wait AS "seconds_in_wait", sql.sql_text AS "sql_text", sess.machine AS "machine", sess.port AS "port"
FROM v$session sess LEFT OUTER JOIN v$sql sql
ON sql.sql_id = sess.sql_id
WHERE sess.type = \'USER\'
ORDER BY PROCESS
');
    }

    function show_status()
    {
        $K = get_rows('SELECT * FROM v$instance');

        return
            reset($K);
    }

    function convert_field($o)
    {
    }

    function unconvert_field($o, $I)
    {
        return $I;
    }

    function support($Qc)
    {
        return
            preg_match('~^(columns|database|drop_col|indexes|processlist|scheme|sql|status|table|variables|view|view_trigger)$~', $Qc);
    }

    $x = "oracle";
    $zi = [];
    $Dh = [];
    foreach ([lang(27) => ["number" => 38, "binary_float" => 12, "binary_double" => 21], lang(28) => ["date" => 10, "timestamp" => 29, "interval year" => 12, "interval day" => 28], lang(25) => ["char" => 2000, "varchar2" => 4000, "nchar" => 2000, "nvarchar2" => 4000, "clob" => 4294967295, "nclob" => 4294967295], lang(29) => ["raw" => 2000, "long raw" => 2147483648, "blob" => 4294967295, "bfile" => 4294967296],] as $y => $X) {
        $zi += $X;
        $Dh[$y] = array_keys($X);
    }
    $Fi = [];
    $sf = ["=", "<", ">", "<=", ">=", "!=", "LIKE", "LIKE %%", "IN", "IS NULL", "NOT LIKE", "NOT REGEXP", "NOT IN", "IS NOT NULL", "SQL"];
    $ld = ["length", "lower", "round", "upper"];
    $rd = ["avg", "count", "count distinct", "max", "min", "sum"];
    $nc = [["date" => "current_date", "timestamp" => "current_timestamp",], ["number|float|double" => "+/-", "date|timestamp" => "+ interval/- interval", "char|clob" => "||",]];
}
$fc["mssql"] = "MS SQL (beta)";
if (isset($_GET["mssql"])) {
    $eg = ["SQLSRV", "MSSQL", "PDO_DBLIB"];
    define("DRIVER", "mssql");
    if (extension_loaded("sqlsrv")) {
        class Min_DB
        {
            public $extension = "sqlsrv";

            public $_link;

            public $_result;

            public $server_info;

            public $affected_rows;

            public $errno;

            public $error;

            public function _get_error()
            {
                $this->error = "";
                foreach (sqlsrv_errors() as $n) {
                    $this->errno = $n["code"];
                    $this->error .= "$n[message]\n";
                }
                $this->error = rtrim($this->error);
            }

            public function connect($N, $V, $F)
            {
                $this->_link = @sqlsrv_connect(preg_replace('~:~', ',', $N), ["UID" => $V, "PWD" => $F, "CharacterSet" => "UTF-8"]);
                if ($this->_link) {
                    $Od = sqlsrv_server_info($this->_link);
                    $this->server_info = $Od['SQLServerVersion'];
                } else {
                    $this->_get_error();
                }

                return (bool)$this->_link;
            }

            public function quote($Q)
            {
                return "'" . str_replace("'", "''", $Q) . "'";
            }

            public function select_db($j)
            {
                return $this->query("USE " . idf_escape($j));
            }

            public function query($G, $_i = false)
            {
                $H = sqlsrv_query($this->_link, $G);
                $this->error = "";
                if (!$H) {
                    $this->_get_error();

                    return
                        false;
                }

                return $this->store_result($H);
            }

            public function multi_query($G)
            {
                $this->_result = sqlsrv_query($this->_link, $G);
                $this->error = "";
                if (!$this->_result) {
                    $this->_get_error();

                    return
                        false;
                }

                return
                    true;
            }

            public function store_result($H = null)
            {
                if (!$H) {
                    $H = $this->_result;
                }
                if (!$H) {
                    return
                    false;
                }
                if (sqlsrv_field_metadata($H)) {
                    return
                    new
                    Min_Result($H);
                }
                $this->affected_rows = sqlsrv_rows_affected($H);

                return
                    true;
            }

            public function next_result()
            {
                return $this->_result ? sqlsrv_next_result($this->_result) : null;
            }

            public function result($G, $o = 0)
            {
                $H = $this->query($G);
                if (!is_object($H)) {
                    return
                    false;
                }
                $J = $H->fetch_row();

                return $J[$o];
            }
        }

        class Min_Result
        {
            public $_result;

            public $_offset = 0;

            public $_fields;

            public $num_rows;

            public function __construct($H)
            {
                $this->_result = $H;
            }

            public function _convert($J)
            {
                foreach ((array)$J
                         as $y => $X) {
                    if (is_a($X, 'DateTime')) {
                        $J[$y] = $X->format("Y-m-d H:i:s");
                    }
                }

                return $J;
            }

            public function fetch_assoc()
            {
                return $this->_convert(sqlsrv_fetch_array($this->_result, SQLSRV_FETCH_ASSOC));
            }

            public function fetch_row()
            {
                return $this->_convert(sqlsrv_fetch_array($this->_result, SQLSRV_FETCH_NUMERIC));
            }

            public function fetch_field()
            {
                if (!$this->_fields) {
                    $this->_fields = sqlsrv_field_metadata($this->_result);
                }
                $o = $this->_fields[$this->_offset++];
                $I = new
                stdClass;
                $I->name = $o["Name"];
                $I->orgname = $o["Name"];
                $I->type = ($o["Type"] == 1 ? 254 : 0);

                return $I;
            }

            public function seek($D)
            {
                for ($s = 0; $s < $D; $s++) {
                    sqlsrv_fetch($this->_result);
                }
            }

            public function __destruct()
            {
                sqlsrv_free_stmt($this->_result);
            }
        }
    } elseif (extension_loaded("mssql")) {
        class Min_DB
        {
            public $extension = "MSSQL";

            public $_link;

            public $_result;

            public $server_info;

            public $affected_rows;

            public $error;

            public function connect($N, $V, $F)
            {
                $this->_link = @mssql_connect($N, $V, $F);
                if ($this->_link) {
                    $H = $this->query("SELECT SERVERPROPERTY('ProductLevel'), SERVERPROPERTY('Edition')");
                    if ($H) {
                        $J = $H->fetch_row();
                        $this->server_info = $this->result("sp_server_info 2", 2) . " [$J[0]] $J[1]";
                    }
                } else {
                    $this->error = mssql_get_last_message();
                }

                return (bool)$this->_link;
            }

            public function quote($Q)
            {
                return "'" . str_replace("'", "''", $Q) . "'";
            }

            public function select_db($j)
            {
                return
                    mssql_select_db($j);
            }

            public function query($G, $_i = false)
            {
                $H = @mssql_query($G, $this->_link);
                $this->error = "";
                if (!$H) {
                    $this->error = mssql_get_last_message();

                    return
                        false;
                }
                if ($H === true) {
                    $this->affected_rows = mssql_rows_affected($this->_link);

                    return
                        true;
                }

                return
                    new
                    Min_Result($H);
            }

            public function multi_query($G)
            {
                return $this->_result = $this->query($G);
            }

            public function store_result()
            {
                return $this->_result;
            }

            public function next_result()
            {
                return
                    mssql_next_result($this->_result->_result);
            }

            public function result($G, $o = 0)
            {
                $H = $this->query($G);
                if (!is_object($H)) {
                    return
                    false;
                }

                return
                    mssql_result($H->_result, 0, $o);
            }
        }

        class Min_Result
        {
            public $_result;

            public $_offset = 0;

            public $_fields;

            public $num_rows;

            public function __construct($H)
            {
                $this->_result = $H;
                $this->num_rows = mssql_num_rows($H);
            }

            public function fetch_assoc()
            {
                return
                    mssql_fetch_assoc($this->_result);
            }

            public function fetch_row()
            {
                return
                    mssql_fetch_row($this->_result);
            }

            public function num_rows()
            {
                return
                    mssql_num_rows($this->_result);
            }

            public function fetch_field()
            {
                $I = mssql_fetch_field($this->_result);
                $I->orgtable = $I->table;
                $I->orgname = $I->name;

                return $I;
            }

            public function seek($D)
            {
                mssql_data_seek($this->_result, $D);
            }

            public function __destruct()
            {
                mssql_free_result($this->_result);
            }
        }
    } elseif (extension_loaded("pdo_dblib")) {
        class Min_DB extends
            Min_PDO
        {
            public $extension = "PDO_DBLIB";

            public function connect($N, $V, $F)
            {
                $this->dsn("dblib:charset=utf8;host=" . str_replace(":", ";unix_socket=", preg_replace('~:(\d)~', ';port=\1', $N)), $V, $F);

                return
                    true;
            }

            public function select_db($j)
            {
                return $this->query("USE " . idf_escape($j));
            }
        }
    }

    class Min_Driver extends
        Min_SQL
    {
        public function insertUpdate($R, $K, $hg)
        {
            foreach ($K
                     as $O) {
                $Gi = [];
                $Z = [];
                foreach ($O
                         as $y => $X) {
                    $Gi[] = "$y = $X";
                    if (isset($hg[idf_unescape($y)])) {
                        $Z[] = "$y = $X";
                    }
                }
                if (!queries("MERGE " . table($R) . " USING (VALUES(" . implode(", ", $O) . ")) AS source (c" . implode(", c", range(1, count($O))) . ") ON " . implode(" AND ", $Z) . " WHEN MATCHED THEN UPDATE SET " . implode(", ", $Gi) . " WHEN NOT MATCHED THEN INSERT (" . implode(", ", array_keys($O)) . ") VALUES (" . implode(", ", $O) . ");")) {
                    return
                    false;
                }
            }

            return
                true;
        }

        public function begin()
        {
            return
                queries("BEGIN TRANSACTION");
        }
    }

    function idf_escape($u)
    {
        return "[" . str_replace("]", "]]", $u) . "]";
    }

    function table($u)
    {
        return ($_GET["ns"] != "" ? idf_escape($_GET["ns"]) . "." : "") . idf_escape($u);
    }

    function connect()
    {
        global $b;
        $g = new
        Min_DB;
        $Hb = $b->credentials();
        if ($g->connect($Hb[0], $Hb[1], $Hb[2])) {
            return $g;
        }

        return $g->error;
    }

    function get_databases()
    {
        return
            get_vals("SELECT name FROM sys.databases WHERE name NOT IN ('master', 'tempdb', 'model', 'msdb')");
    }

    function limit($G, $Z, $z, $D = 0, $M = " ")
    {
        return ($z !== null ? " TOP (" . ($z + $D) . ")" : "") . " $G$Z";
    }

    function limit1($R, $G, $Z, $M = "\n")
    {
        return
            limit($G, $Z, 1, 0, $M);
    }

    function db_collation($l, $qb)
    {
        global $g;

        return $g->result("SELECT collation_name FROM sys.databases WHERE name = " . q($l));
    }

    function engines()
    {
        return
            [];
    }

    function logged_user()
    {
        global $g;

        return $g->result("SELECT SUSER_NAME()");
    }

    function tables_list()
    {
        return
            get_key_vals("SELECT name, type_desc FROM sys.all_objects WHERE schema_id = SCHEMA_ID(" . q(get_schema()) . ") AND type IN ('S', 'U', 'V') ORDER BY name");
    }

    function count_tables($k)
    {
        global $g;
        $I = [];
        foreach ($k
                 as $l) {
            $g->select_db($l);
            $I[$l] = $g->result("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES");
        }

        return $I;
    }

    function table_status($C = "")
    {
        $I = [];
        foreach (get_rows("SELECT name AS Name, type_desc AS Engine FROM sys.all_objects WHERE schema_id = SCHEMA_ID(" . q(get_schema()) . ") AND type IN ('S', 'U', 'V') " . ($C != "" ? "AND name = " . q($C) : "ORDER BY name")) as $J) {
            if ($C != "") {
                return $J;
            }
            $I[$J["Name"]] = $J;
        }

        return $I;
    }

    function is_view($S)
    {
        return $S["Engine"] == "VIEW";
    }

    function fk_support($S)
    {
        return
            true;
    }

    function fields($R)
    {
        $I = [];
        foreach (get_rows("SELECT c.max_length, c.precision, c.scale, c.name, c.is_nullable, c.is_identity, c.collation_name, t.name type, CAST(d.definition as text) [default]
FROM sys.all_columns c
JOIN sys.all_objects o ON c.object_id = o.object_id
JOIN sys.types t ON c.user_type_id = t.user_type_id
LEFT JOIN sys.default_constraints d ON c.default_object_id = d.parent_column_id
WHERE o.schema_id = SCHEMA_ID(" . q(get_schema()) . ") AND o.type IN ('S', 'U', 'V') AND o.name = " . q($R)) as $J) {
            $U = $J["type"];
            $te = (preg_match("~char|binary~", $U) ? $J["max_length"] : ($U == "decimal" ? "$J[precision],$J[scale]" : ""));
            $I[$J["name"]] = ["field" => $J["name"], "full_type" => $U . ($te ? "($te)" : ""), "type" => $U, "length" => $te, "default" => $J["default"], "null" => $J["is_nullable"], "auto_increment" => $J["is_identity"], "collation" => $J["collation_name"], "privileges" => ["insert" => 1, "select" => 1, "update" => 1], "primary" => $J["is_identity"],];
        }

        return $I;
    }

    function indexes($R, $h = null)
    {
        $I = [];
        foreach (get_rows("SELECT i.name, key_ordinal, is_unique, is_primary_key, c.name AS column_name, is_descending_key
FROM sys.indexes i
INNER JOIN sys.index_columns ic ON i.object_id = ic.object_id AND i.index_id = ic.index_id
INNER JOIN sys.columns c ON ic.object_id = c.object_id AND ic.column_id = c.column_id
WHERE OBJECT_NAME(i.object_id) = " . q($R), $h) as $J) {
            $C = $J["name"];
            $I[$C]["type"] = ($J["is_primary_key"] ? "PRIMARY" : ($J["is_unique"] ? "UNIQUE" : "INDEX"));
            $I[$C]["lengths"] = [];
            $I[$C]["columns"][$J["key_ordinal"]] = $J["column_name"];
            $I[$C]["descs"][$J["key_ordinal"]] = ($J["is_descending_key"] ? '1' : null);
        }

        return $I;
    }

    function view($C)
    {
        global $g;

        return
            ["select" => preg_replace('~^(?:[^[]|\[[^]]*])*\s+AS\s+~isU', '', $g->result("SELECT VIEW_DEFINITION FROM INFORMATION_SCHEMA.VIEWS WHERE TABLE_SCHEMA = SCHEMA_NAME() AND TABLE_NAME = " . q($C)))];
    }

    function collations()
    {
        $I = [];
        foreach (get_vals("SELECT name FROM fn_helpcollations()") as $pb) {
            $I[preg_replace('~_.*~', '', $pb)][] = $pb;
        }

        return $I;
    }

    function information_schema($l)
    {
        return
            false;
    }

    function error()
    {
        global $g;

        return
            nl_br(h(preg_replace('~^(\[[^]]*])+~m', '', $g->error)));
    }

    function create_database($l, $pb)
    {
        return
            queries("CREATE DATABASE " . idf_escape($l) . (preg_match('~^[a-z0-9_]+$~i', $pb) ? " COLLATE $pb" : ""));
    }

    function drop_databases($k)
    {
        return
            queries("DROP DATABASE " . implode(", ", array_map('idf_escape', $k)));
    }

    function rename_database($C, $pb)
    {
        if (preg_match('~^[a-z0-9_]+$~i', $pb)) {
            queries("ALTER DATABASE " . idf_escape(DB) . " COLLATE $pb");
        }
        queries("ALTER DATABASE " . idf_escape(DB) . " MODIFY NAME = " . idf_escape($C));

        return
            true;
    }

    function auto_increment()
    {
        return " IDENTITY" . ($_POST["Auto_increment"] != "" ? "(" . number($_POST["Auto_increment"]) . ",1)" : "") . " PRIMARY KEY";
    }

    function alter_table($R, $C, $p, $dd, $vb, $vc, $pb, $Ma, $Rf)
    {
        $c = [];
        foreach ($p
                 as $o) {
            $d = idf_escape($o[0]);
            $X = $o[1];
            if (!$X) {
                $c["DROP"][] = " COLUMN $d";
            } else {
                $X[1] = preg_replace("~( COLLATE )'(\\w+)'~", '\1\2', $X[1]);
                if ($o[0] == "") {
                    $c["ADD"][] = "\n  " . implode("", $X) . ($R == "" ? substr($dd[$X[0]], 16 + strlen($X[0])) : "");
                } else {
                    unset($X[6]);
                    if ($d != $X[0]) {
                        queries("EXEC sp_rename " . q(table($R) . ".$d") . ", " . q(idf_unescape($X[0])) . ", 'COLUMN'");
                    }
                    $c["ALTER COLUMN " . implode("", $X)][] = "";
                }
            }
        }
        if ($R == "") {
            return
            queries("CREATE TABLE " . table($C) . " (" . implode(",", (array)$c["ADD"]) . "\n)");
        }
        if ($R != $C) {
            queries("EXEC sp_rename " . q(table($R)) . ", " . q($C));
        }
        if ($dd) {
            $c[""] = $dd;
        }
        foreach ($c
                 as $y => $X) {
            if (!queries("ALTER TABLE " . idf_escape($C) . " $y" . implode(",", $X))) {
                return
                false;
            }
        }

        return
            true;
    }

    function alter_indexes($R, $c)
    {
        $v = [];
        $gc = [];
        foreach ($c
                 as $X) {
            if ($X[2] == "DROP") {
                if ($X[0] == "PRIMARY") {
                    $gc[] = idf_escape($X[1]);
                } else {
                    $v[] = idf_escape($X[1]) . " ON " . table($R);
                }
            } elseif (!queries(($X[0] != "PRIMARY" ? "CREATE $X[0] " . ($X[0] != "INDEX" ? "INDEX " : "") . idf_escape($X[1] != "" ? $X[1] : uniqid($R . "_")) . " ON " . table($R) : "ALTER TABLE " . table($R) . " ADD PRIMARY KEY") . " (" . implode(", ", $X[2]) . ")")) {
                return
                false;
            }
        }

        return (!$v || queries("DROP INDEX " . implode(", ", $v))) && (!$gc || queries("ALTER TABLE " . table($R) . " DROP " . implode(", ", $gc)));
    }

    function last_id()
    {
        global $g;

        return $g->result("SELECT SCOPE_IDENTITY()");
    }

    function explain($g, $G)
    {
        $g->query("SET SHOWPLAN_ALL ON");
        $I = $g->query($G);
        $g->query("SET SHOWPLAN_ALL OFF");

        return $I;
    }

    function found_rows($S, $Z)
    {
    }

    function foreign_keys($R)
    {
        $I = [];
        foreach (get_rows("EXEC sp_fkeys @fktable_name = " . q($R)) as $J) {
            $q =& $I[$J["FK_NAME"]];
            $q["table"] = $J["PKTABLE_NAME"];
            $q["source"][] = $J["FKCOLUMN_NAME"];
            $q["target"][] = $J["PKCOLUMN_NAME"];
        }

        return $I;
    }

    function truncate_tables($T)
    {
        return
            apply_queries("TRUNCATE TABLE", $T);
    }

    function drop_views($Wi)
    {
        return
            queries("DROP VIEW " . implode(", ", array_map('table', $Wi)));
    }

    function drop_tables($T)
    {
        return
            queries("DROP TABLE " . implode(", ", array_map('table', $T)));
    }

    function move_tables($T, $Wi, $Th)
    {
        return
            apply_queries("ALTER SCHEMA " . idf_escape($Th) . " TRANSFER", array_merge($T, $Wi));
    }

    function trigger($C)
    {
        if ($C == "") {
            return
            [];
        }
        $K = get_rows("SELECT s.name [Trigger],
CASE WHEN OBJECTPROPERTY(s.id, 'ExecIsInsertTrigger') = 1 THEN 'INSERT' WHEN OBJECTPROPERTY(s.id, 'ExecIsUpdateTrigger') = 1 THEN 'UPDATE' WHEN OBJECTPROPERTY(s.id, 'ExecIsDeleteTrigger') = 1 THEN 'DELETE' END [Event],
CASE WHEN OBJECTPROPERTY(s.id, 'ExecIsInsteadOfTrigger') = 1 THEN 'INSTEAD OF' ELSE 'AFTER' END [Timing],
c.text
FROM sysobjects s
JOIN syscomments c ON s.id = c.id
WHERE s.xtype = 'TR' AND s.name = " . q($C));
        $I = reset($K);
        if ($I) {
            $I["Statement"] = preg_replace('~^.+\s+AS\s+~isU', '', $I["text"]);
        }

        return $I;
    }

    function triggers($R)
    {
        $I = [];
        foreach (get_rows("SELECT sys1.name,
CASE WHEN OBJECTPROPERTY(sys1.id, 'ExecIsInsertTrigger') = 1 THEN 'INSERT' WHEN OBJECTPROPERTY(sys1.id, 'ExecIsUpdateTrigger') = 1 THEN 'UPDATE' WHEN OBJECTPROPERTY(sys1.id, 'ExecIsDeleteTrigger') = 1 THEN 'DELETE' END [Event],
CASE WHEN OBJECTPROPERTY(sys1.id, 'ExecIsInsteadOfTrigger') = 1 THEN 'INSTEAD OF' ELSE 'AFTER' END [Timing]
FROM sysobjects sys1
JOIN sysobjects sys2 ON sys1.parent_obj = sys2.id
WHERE sys1.xtype = 'TR' AND sys2.name = " . q($R)) as $J) {
            $I[$J["name"]] = [$J["Timing"], $J["Event"]];
        }

        return $I;
    }

    function trigger_options()
    {
        return
            ["Timing" => ["AFTER", "INSTEAD OF"], "Event" => ["INSERT", "UPDATE", "DELETE"], "Type" => ["AS"],];
    }

    function schemas()
    {
        return
            get_vals("SELECT name FROM sys.schemas");
    }

    function get_schema()
    {
        global $g;
        if ($_GET["ns"] != "") {
            return $_GET["ns"];
        }

        return $g->result("SELECT SCHEMA_NAME()");
    }

    function set_schema($Xg)
    {
        return
            true;
    }

    function use_sql($j)
    {
        return "USE " . idf_escape($j);
    }

    function show_variables()
    {
        return
            [];
    }

    function show_status()
    {
        return
            [];
    }

    function convert_field($o)
    {
    }

    function unconvert_field($o, $I)
    {
        return $I;
    }

    function support($Qc)
    {
        return
            preg_match('~^(columns|database|drop_col|indexes|scheme|sql|table|trigger|view|view_trigger)$~', $Qc);
    }

    $x = "mssql";
    $zi = [];
    $Dh = [];
    foreach ([lang(27) => ["tinyint" => 3, "smallint" => 5, "int" => 10, "bigint" => 20, "bit" => 1, "decimal" => 0, "real" => 12, "float" => 53, "smallmoney" => 10, "money" => 20], lang(28) => ["date" => 10, "smalldatetime" => 19, "datetime" => 19, "datetime2" => 19, "time" => 8, "datetimeoffset" => 10], lang(25) => ["char" => 8000, "varchar" => 8000, "text" => 2147483647, "nchar" => 4000, "nvarchar" => 4000, "ntext" => 1073741823], lang(29) => ["binary" => 8000, "varbinary" => 8000, "image" => 2147483647],] as $y => $X) {
        $zi += $X;
        $Dh[$y] = array_keys($X);
    }
    $Fi = [];
    $sf = ["=", "<", ">", "<=", ">=", "!=", "LIKE", "LIKE %%", "IN", "IS NULL", "NOT LIKE", "NOT IN", "IS NOT NULL"];
    $ld = ["len", "lower", "round", "upper"];
    $rd = ["avg", "count", "count distinct", "max", "min", "sum"];
    $nc = [["date|time" => "getdate",], ["int|decimal|real|float|money|datetime" => "+/-", "char|text" => "+",]];
}
$fc['firebird'] = 'Firebird (alpha)';
if (isset($_GET["firebird"])) {
    $eg = ["interbase"];
    define("DRIVER", "firebird");
    if (extension_loaded("interbase")) {
        class Min_DB
        {
            public $extension = "Firebird";

            public $server_info;

            public $affected_rows;

            public $errno;

            public $error;

            public $_link;

            public $_result;

            public function connect($N, $V, $F)
            {
                $this->_link = ibase_connect($N, $V, $F);
                if ($this->_link) {
                    $Ji = explode(':', $N);
                    $this->service_link = ibase_service_attach($Ji[0], $V, $F);
                    $this->server_info = ibase_server_info($this->service_link, IBASE_SVC_SERVER_VERSION);
                } else {
                    $this->errno = ibase_errcode();
                    $this->error = ibase_errmsg();
                }

                return (bool)$this->_link;
            }

            public function quote($Q)
            {
                return "'" . str_replace("'", "''", $Q) . "'";
            }

            public function select_db($j)
            {
                return ($j == "domain");
            }

            public function query($G, $_i = false)
            {
                $H = ibase_query($G, $this->_link);
                if (!$H) {
                    $this->errno = ibase_errcode();
                    $this->error = ibase_errmsg();

                    return
                        false;
                }
                $this->error = "";
                if ($H === true) {
                    $this->affected_rows = ibase_affected_rows($this->_link);

                    return
                        true;
                }

                return
                    new
                    Min_Result($H);
            }

            public function multi_query($G)
            {
                return $this->_result = $this->query($G);
            }

            public function store_result()
            {
                return $this->_result;
            }

            public function next_result()
            {
                return
                    false;
            }

            public function result($G, $o = 0)
            {
                $H = $this->query($G);
                if (!$H || !$H->num_rows) {
                    return
                    false;
                }
                $J = $H->fetch_row();

                return $J[$o];
            }
        }

        class Min_Result
        {
            public $num_rows;

            public $_result;

            public $_offset = 0;

            public function __construct($H)
            {
                $this->_result = $H;
            }

            public function fetch_assoc()
            {
                return
                    ibase_fetch_assoc($this->_result);
            }

            public function fetch_row()
            {
                return
                    ibase_fetch_row($this->_result);
            }

            public function fetch_field()
            {
                $o = ibase_field_info($this->_result, $this->_offset++);

                return (object)['name' => $o['name'], 'orgname' => $o['name'], 'type' => $o['type'], 'charsetnr' => $o['length'],];
            }

            public function __destruct()
            {
                ibase_free_result($this->_result);
            }
        }
    }

    class Min_Driver extends
        Min_SQL
    {
    }

    function idf_escape($u)
    {
        return '"' . str_replace('"', '""', $u) . '"';
    }

    function table($u)
    {
        return
            idf_escape($u);
    }

    function connect()
    {
        global $b;
        $g = new
        Min_DB;
        $Hb = $b->credentials();
        if ($g->connect($Hb[0], $Hb[1], $Hb[2])) {
            return $g;
        }

        return $g->error;
    }

    function get_databases($bd)
    {
        return
            ["domain"];
    }

    function limit($G, $Z, $z, $D = 0, $M = " ")
    {
        $I = '';
        $I .= ($z !== null ? $M . "FIRST $z" . ($D ? " SKIP $D" : "") : "");
        $I .= " $G$Z";

        return $I;
    }

    function limit1($R, $G, $Z, $M = "\n")
    {
        return
            limit($G, $Z, 1, 0, $M);
    }

    function db_collation($l, $qb)
    {
    }

    function engines()
    {
        return
            [];
    }

    function logged_user()
    {
        global $b;
        $Hb = $b->credentials();

        return $Hb[1];
    }

    function tables_list()
    {
        global $g;
        $G = 'SELECT RDB$RELATION_NAME FROM rdb$relations WHERE rdb$system_flag = 0';
        $H = ibase_query($g->_link, $G);
        $I = [];
        while ($J = ibase_fetch_assoc($H)) {
            $I[$J['RDB$RELATION_NAME']] = 'table';
        }
        ksort($I);

        return $I;
    }

    function count_tables($k)
    {
        return
            [];
    }

    function table_status($C = "", $Pc = false)
    {
        global $g;
        $I = [];
        $Mb = tables_list();
        foreach ($Mb
                 as $v => $X) {
            $v = trim($v);
            $I[$v] = ['Name' => $v, 'Engine' => 'standard',];
            if ($C == $v) {
                return $I[$v];
            }
        }

        return $I;
    }

    function is_view($S)
    {
        return
            false;
    }

    function fk_support($S)
    {
        return
            preg_match('~InnoDB|IBMDB2I~i', $S["Engine"]);
    }

    function fields($R)
    {
        global $g;
        $I = [];
        $G = 'SELECT r.RDB$FIELD_NAME AS field_name,
r.RDB$DESCRIPTION AS field_description,
r.RDB$DEFAULT_VALUE AS field_default_value,
r.RDB$NULL_FLAG AS field_not_null_constraint,
f.RDB$FIELD_LENGTH AS field_length,
f.RDB$FIELD_PRECISION AS field_precision,
f.RDB$FIELD_SCALE AS field_scale,
CASE f.RDB$FIELD_TYPE
WHEN 261 THEN \'BLOB\'
WHEN 14 THEN \'CHAR\'
WHEN 40 THEN \'CSTRING\'
WHEN 11 THEN \'D_FLOAT\'
WHEN 27 THEN \'DOUBLE\'
WHEN 10 THEN \'FLOAT\'
WHEN 16 THEN \'INT64\'
WHEN 8 THEN \'INTEGER\'
WHEN 9 THEN \'QUAD\'
WHEN 7 THEN \'SMALLINT\'
WHEN 12 THEN \'DATE\'
WHEN 13 THEN \'TIME\'
WHEN 35 THEN \'TIMESTAMP\'
WHEN 37 THEN \'VARCHAR\'
ELSE \'UNKNOWN\'
END AS field_type,
f.RDB$FIELD_SUB_TYPE AS field_subtype,
coll.RDB$COLLATION_NAME AS field_collation,
cset.RDB$CHARACTER_SET_NAME AS field_charset
FROM RDB$RELATION_FIELDS r
LEFT JOIN RDB$FIELDS f ON r.RDB$FIELD_SOURCE = f.RDB$FIELD_NAME
LEFT JOIN RDB$COLLATIONS coll ON f.RDB$COLLATION_ID = coll.RDB$COLLATION_ID
LEFT JOIN RDB$CHARACTER_SETS cset ON f.RDB$CHARACTER_SET_ID = cset.RDB$CHARACTER_SET_ID
WHERE r.RDB$RELATION_NAME = ' . q($R) . '
ORDER BY r.RDB$FIELD_POSITION';
        $H = ibase_query($g->_link, $G);
        while ($J = ibase_fetch_assoc($H)) {
            $I[trim($J['FIELD_NAME'])] = ["field" => trim($J["FIELD_NAME"]), "full_type" => trim($J["FIELD_TYPE"]), "type" => trim($J["FIELD_SUB_TYPE"]), "default" => trim($J['FIELD_DEFAULT_VALUE']), "null" => (trim($J["FIELD_NOT_NULL_CONSTRAINT"]) == "YES"), "auto_increment" => '0', "collation" => trim($J["FIELD_COLLATION"]), "privileges" => ["insert" => 1, "select" => 1, "update" => 1], "comment" => trim($J["FIELD_DESCRIPTION"]),];
        }

        return $I;
    }

    function indexes($R, $h = null)
    {
        $I = [];

        return $I;
    }

    function foreign_keys($R)
    {
        return
            [];
    }

    function collations()
    {
        return
            [];
    }

    function information_schema($l)
    {
        return
            false;
    }

    function error()
    {
        global $g;

        return
            h($g->error);
    }

    function types()
    {
        return
            [];
    }

    function schemas()
    {
        return
            [];
    }

    function get_schema()
    {
        return "";
    }

    function set_schema($Xg)
    {
        return
            true;
    }

    function support($Qc)
    {
        return
            preg_match("~^(columns|sql|status|table)$~", $Qc);
    }

    $x = "firebird";
    $sf = ["="];
    $ld = [];
    $rd = [];
    $nc = [];
}
$fc["simpledb"] = "SimpleDB";
if (isset($_GET["simpledb"])) {
    $eg = ["SimpleXML + allow_url_fopen"];
    define("DRIVER", "simpledb");
    if (class_exists('SimpleXMLElement') && ini_bool('allow_url_fopen')) {
        class Min_DB
        {
            public $extension = "SimpleXML";

            public $server_info = '2009-04-15';

            public $error;

            public $timeout;

            public $next;

            public $affected_rows;

            public $_result;

            public function select_db($j)
            {
                return ($j == "domain");
            }

            public function query($G, $_i = false)
            {
                $Lf = ['SelectExpression' => $G, 'ConsistentRead' => 'true'];
                if ($this->next) {
                    $Lf['NextToken'] = $this->next;
                }
                $H = sdb_request_all('Select', 'Item', $Lf, $this->timeout);
                $this->timeout = 0;
                if ($H === false) {
                    return $H;
                }
                if (preg_match('~^\s*SELECT\s+COUNT\(~i', $G)) {
                    $Hh = 0;
                    foreach ($H
                             as $ae) {
                        $Hh += $ae->Attribute->Value;
                    }
                    $H = [(object)['Attribute' => [(object)['Name' => 'Count', 'Value' => $Hh,]]]];
                }

                return
                    new
                    Min_Result($H);
            }

            public function multi_query($G)
            {
                return $this->_result = $this->query($G);
            }

            public function store_result()
            {
                return $this->_result;
            }

            public function next_result()
            {
                return
                    false;
            }

            public function quote($Q)
            {
                return "'" . str_replace("'", "''", $Q) . "'";
            }
        }

        class Min_Result
        {
            public $num_rows;

            public $_rows = [];

            public $_offset = 0;

            public function __construct($H)
            {
                foreach ($H
                         as $ae) {
                    $J = [];
                    if ($ae->Name != '') {
                        $J['itemName()'] = (string)$ae->Name;
                    }
                    foreach ($ae->Attribute
                             as $Ja) {
                        $C = $this->_processValue($Ja->Name);
                        $Y = $this->_processValue($Ja->Value);
                        if (isset($J[$C])) {
                            $J[$C] = (array)$J[$C];
                            $J[$C][] = $Y;
                        } else {
                            $J[$C] = $Y;
                        }
                    }
                    $this->_rows[] = $J;
                    foreach ($J
                             as $y => $X) {
                        if (!isset($this->_rows[0][$y])) {
                            $this->_rows[0][$y] = null;
                        }
                    }
                }
                $this->num_rows = count($this->_rows);
            }

            public function _processValue($qc)
            {
                return (is_object($qc) && $qc['encoding'] == 'base64' ? base64_decode($qc) : (string)$qc);
            }

            public function fetch_assoc()
            {
                $J = current($this->_rows);
                if (!$J) {
                    return $J;
                }
                $I = [];
                foreach ($this->_rows[0] as $y => $X) {
                    $I[$y] = $J[$y];
                }
                next($this->_rows);

                return $I;
            }

            public function fetch_row()
            {
                $I = $this->fetch_assoc();
                if (!$I) {
                    return $I;
                }

                return
                    array_values($I);
            }

            public function fetch_field()
            {
                $ge = array_keys($this->_rows[0]);

                return (object)['name' => $ge[$this->_offset++]];
            }
        }
    }

    class Min_Driver extends
        Min_SQL
    {
        public $hg = "itemName()";

        public function _chunkRequest($Ed, $wa, $Lf, $Fc = [])
        {
            global $g;
            foreach (array_chunk($Ed, 25) as $ib) {
                $Mf = $Lf;
                foreach ($ib
                         as $s => $t) {
                    $Mf["Item.$s.ItemName"] = $t;
                    foreach ($Fc
                             as $y => $X) {
                        $Mf["Item.$s.$y"] = $X;
                    }
                }
                if (!sdb_request($wa, $Mf)) {
                    return
                    false;
                }
            }
            $g->affected_rows = count($Ed);

            return
                true;
        }

        public function _extractIds($R, $tg, $z)
        {
            $I = [];
            if (preg_match_all("~itemName\(\) = (('[^']*+')+)~", $tg, $Ce)) {
                $I = array_map('idf_unescape', $Ce[1]);
            } else {
                foreach (sdb_request_all('Select', 'Item', ['SelectExpression' => 'SELECT itemName() FROM ' . table($R) . $tg . ($z ? " LIMIT 1" : "")]) as $ae) {
                    $I[] = $ae->Name;
                }
            }

            return $I;
        }

        public function select($R, $L, $Z, $od, $xf = [], $z = 1, $E = 0, $jg = false)
        {
            global $g;
            $g->next = $_GET["next"];
            $I = parent::select($R, $L, $Z, $od, $xf, $z, $E, $jg);
            $g->next = 0;

            return $I;
        }

        public function delete($R, $tg, $z = 0)
        {
            return $this->_chunkRequest($this->_extractIds($R, $tg, $z), 'BatchDeleteAttributes', ['DomainName' => $R]);
        }

        public function update($R, $O, $tg, $z = 0, $M = "\n")
        {
            $Vb = [];
            $Sd = [];
            $s = 0;
            $Ed = $this->_extractIds($R, $tg, $z);
            $t = idf_unescape($O["`itemName()`"]);
            unset($O["`itemName()`"]);
            foreach ($O
                     as $y => $X) {
                $y = idf_unescape($y);
                if ($X == "NULL" || ($t != "" && [$t] != $Ed)) {
                    $Vb["Attribute." . count($Vb) . ".Name"] = $y;
                }
                if ($X != "NULL") {
                    foreach ((array)$X
                             as $ce => $W) {
                        $Sd["Attribute.$s.Name"] = $y;
                        $Sd["Attribute.$s.Value"] = (is_array($X) ? $W : idf_unescape($W));
                        if (!$ce) {
                            $Sd["Attribute.$s.Replace"] = "true";
                        }
                        $s++;
                    }
                }
            }
            $Lf = ['DomainName' => $R];

            return (!$Sd || $this->_chunkRequest(($t != "" ? [$t] : $Ed), 'BatchPutAttributes', $Lf, $Sd)) && (!$Vb || $this->_chunkRequest($Ed, 'BatchDeleteAttributes', $Lf, $Vb));
        }

        public function insert($R, $O)
        {
            $Lf = ["DomainName" => $R];
            $s = 0;
            foreach ($O
                     as $C => $Y) {
                if ($Y != "NULL") {
                    $C = idf_unescape($C);
                    if ($C == "itemName()") {
                        $Lf["ItemName"] = idf_unescape($Y);
                    } else {
                        foreach ((array)$Y
                                 as $X) {
                            $Lf["Attribute.$s.Name"] = $C;
                            $Lf["Attribute.$s.Value"] = (is_array($Y) ? $X : idf_unescape($Y));
                            $s++;
                        }
                    }
                }
            }

            return
                sdb_request('PutAttributes', $Lf);
        }

        public function insertUpdate($R, $K, $hg)
        {
            foreach ($K
                     as $O) {
                if (!$this->update($R, $O, "WHERE `itemName()` = " . q($O["`itemName()`"]))) {
                    return
                    false;
                }
            }

            return
                true;
        }

        public function begin()
        {
            return
                false;
        }

        public function commit()
        {
            return
                false;
        }

        public function rollback()
        {
            return
                false;
        }

        public function slowQuery($G, $bi)
        {
            $this->_conn->timeout = $bi;

            return $G;
        }
    }

    function connect()
    {
        global $b;
        list(, , $F) = $b->credentials();
        if ($F != "") {
            return
            lang(22);
        }

        return
            new
            Min_DB;
    }

    function support($Qc)
    {
        return
            preg_match('~sql~', $Qc);
    }

    function logged_user()
    {
        global $b;
        $Hb = $b->credentials();

        return $Hb[1];
    }

    function get_databases()
    {
        return
            ["domain"];
    }

    function collations()
    {
        return
            [];
    }

    function db_collation($l, $qb)
    {
    }

    function tables_list()
    {
        global $g;
        $I = [];
        foreach (sdb_request_all('ListDomains', 'DomainName') as $R) {
            $I[(string)$R] = 'table';
        }
        if ($g->error && defined("PAGE_HEADER")) {
            echo "<p class='error'>" . error() . "\n";
        }

        return $I;
    }

    function table_status($C = "", $Pc = false)
    {
        $I = [];
        foreach (($C != "" ? [$C => true] : tables_list()) as $R => $U) {
            $J = ["Name" => $R, "Auto_increment" => ""];
            if (!$Pc) {
                $Pe = sdb_request('DomainMetadata', ['DomainName' => $R]);
                if ($Pe) {
                    foreach (["Rows" => "ItemCount", "Data_length" => "ItemNamesSizeBytes", "Index_length" => "AttributeValuesSizeBytes", "Data_free" => "AttributeNamesSizeBytes",] as $y => $X) {
                        $J[$y] = (string)$Pe->$X;
                    }
                }
            }
            if ($C != "") {
                return $J;
            }
            $I[$R] = $J;
        }

        return $I;
    }

    function explain($g, $G)
    {
    }

    function error()
    {
        global $g;

        return
            h($g->error);
    }

    function information_schema()
    {
    }

    function is_view($S)
    {
    }

    function indexes($R, $h = null)
    {
        return
            [["type" => "PRIMARY", "columns" => ["itemName()"]],];
    }

    function fields($R)
    {
        return
            fields_from_edit();
    }

    function foreign_keys($R)
    {
        return
            [];
    }

    function table($u)
    {
        return
            idf_escape($u);
    }

    function idf_escape($u)
    {
        return "`" . str_replace("`", "``", $u) . "`";
    }

    function limit($G, $Z, $z, $D = 0, $M = " ")
    {
        return " $G$Z" . ($z !== null ? $M . "LIMIT $z" : "");
    }

    function unconvert_field($o, $I)
    {
        return $I;
    }

    function fk_support($S)
    {
    }

    function engines()
    {
        return
            [];
    }

    function alter_table($R, $C, $p, $dd, $vb, $vc, $pb, $Ma, $Rf)
    {
        return ($R == "" && sdb_request('CreateDomain', ['DomainName' => $C]));
    }

    function drop_tables($T)
    {
        foreach ($T
                 as $R) {
            if (!sdb_request('DeleteDomain', ['DomainName' => $R])) {
                return
                false;
            }
        }

        return
            true;
    }

    function count_tables($k)
    {
        foreach ($k
                 as $l) {
            return
            [$l => count(tables_list())];
        }
    }

    function found_rows($S, $Z)
    {
        return ($Z ? null : $S["Rows"]);
    }

    function last_id()
    {
    }

    function hmac($Ca, $Mb, $y, $xg = false)
    {
        $Va = 64;
        if (strlen($y) > $Va) {
            $y = pack("H*", $Ca($y));
        }
        $y = str_pad($y, $Va, "\0");
        $de = $y ^ str_repeat("\x36", $Va);
        $ee = $y ^ str_repeat("\x5C", $Va);
        $I = $Ca($ee . pack("H*", $Ca($de . $Mb)));
        if ($xg) {
            $I = pack("H*", $I);
        }

        return $I;
    }

    function sdb_request($wa, $Lf = [])
    {
        global $b, $g;
        list($Bd, $Lf['AWSAccessKeyId'], $ah) = $b->credentials();
        $Lf['Action'] = $wa;
        $Lf['Timestamp'] = gmdate('Y-m-d\TH:i:s+00:00');
        $Lf['Version'] = '2009-04-15';
        $Lf['SignatureVersion'] = 2;
        $Lf['SignatureMethod'] = 'HmacSHA1';
        ksort($Lf);
        $G = '';
        foreach ($Lf
                 as $y => $X) {
            $G .= '&' . rawurlencode($y) . '=' . rawurlencode($X);
        }
        $G = str_replace('%7E', '~', substr($G, 1));
        $G .= "&Signature=" . urlencode(base64_encode(hmac('sha1', "POST\n" . preg_replace('~^https?://~', '', $Bd) . "\n/\n$G", $ah, true)));
        @ini_set('track_errors', 1);
        $Uc = @file_get_contents((preg_match('~^https?://~', $Bd) ? $Bd : "http://$Bd"), false, stream_context_create(['http' => ['method' => 'POST', 'content' => $G, 'ignore_errors' => 1,]]));
        if (!$Uc) {
            $g->error = $php_errormsg;

            return
                false;
        }
        libxml_use_internal_errors(true);
        $jj = simplexml_load_string($Uc);
        if (!$jj) {
            $n = libxml_get_last_error();
            $g->error = $n->message;

            return
                false;
        }
        if ($jj->Errors) {
            $n = $jj->Errors->Error;
            $g->error = "$n->Message ($n->Code)";

            return
                false;
        }
        $g->error = '';
        $Sh = $wa . "Result";

        return ($jj->$Sh ? $jj->$Sh : true);
    }

    function sdb_request_all($wa, $Sh, $Lf = [], $bi = 0)
    {
        $I = [];
        $_h = ($bi ? microtime(true) : 0);
        $z = (preg_match('~LIMIT\s+(\d+)\s*$~i', $Lf['SelectExpression'], $B) ? $B[1] : 0);
        do {
            $jj = sdb_request($wa, $Lf);
            if (!$jj) {
                break;
            }
            foreach ($jj->$Sh
                     as $qc) {
                $I[] = $qc;
            }
            if ($z && count($I) >= $z) {
                $_GET["next"] = $jj->NextToken;
                break;
            }
            if ($bi && microtime(true) - $_h > $bi) {
                return
                false;
            }
            $Lf['NextToken'] = $jj->NextToken;
            if ($z) {
                $Lf['SelectExpression'] = preg_replace('~\d+\s*$~', $z - count($I), $Lf['SelectExpression']);
            }
        } while ($jj->NextToken);

        return $I;
    }

    $x = "simpledb";
    $sf = ["=", "<", ">", "<=", ">=", "!=", "LIKE", "LIKE %%", "IN", "IS NULL", "NOT LIKE", "IS NOT NULL"];
    $ld = [];
    $rd = ["count"];
    $nc = [["json"]];
}
$fc["mongo"] = "MongoDB";
if (isset($_GET["mongo"])) {
    $eg = ["mongo", "mongodb"];
    define("DRIVER", "mongo");
    if (class_exists('MongoDB')) {
        class Min_DB
        {
            public $extension = "Mongo";

            public $server_info = MongoClient::VERSION;

            public $error;

            public $last_id;

            public $_link;

            public $_db;

            public function connect($Hi, $vf)
            {
                return @new
                MongoClient($Hi, $vf);
            }

            public function query($G)
            {
                return
                    false;
            }

            public function select_db($j)
            {
                try {
                    $this->_db = $this->_link->selectDB($j);

                    return
                        true;
                } catch (Exception$Bc) {
                    $this->error = $Bc->getMessage();

                    return
                        false;
                }
            }

            public function quote($Q)
            {
                return $Q;
            }
        }

        class Min_Result
        {
            public $num_rows;

            public $_rows = [];

            public $_offset = 0;

            public $_charset = [];

            public function __construct($H)
            {
                foreach ($H
                         as $ae) {
                    $J = [];
                    foreach ($ae
                             as $y => $X) {
                        if (is_a($X, 'MongoBinData')) {
                            $this->_charset[$y] = 63;
                        }
                        $J[$y] = (is_a($X, 'MongoId') ? 'ObjectId("' . strval($X) . '")' : (is_a($X, 'MongoDate') ? gmdate("Y-m-d H:i:s", $X->sec) . " GMT" : (is_a($X, 'MongoBinData') ? $X->bin : (is_a($X, 'MongoRegex') ? strval($X) : (is_object($X) ? get_class($X) : $X)))));
                    }
                    $this->_rows[] = $J;
                    foreach ($J
                             as $y => $X) {
                        if (!isset($this->_rows[0][$y])) {
                            $this->_rows[0][$y] = null;
                        }
                    }
                }
                $this->num_rows = count($this->_rows);
            }

            public function fetch_assoc()
            {
                $J = current($this->_rows);
                if (!$J) {
                    return $J;
                }
                $I = [];
                foreach ($this->_rows[0] as $y => $X) {
                    $I[$y] = $J[$y];
                }
                next($this->_rows);

                return $I;
            }

            public function fetch_row()
            {
                $I = $this->fetch_assoc();
                if (!$I) {
                    return $I;
                }

                return
                    array_values($I);
            }

            public function fetch_field()
            {
                $ge = array_keys($this->_rows[0]);
                $C = $ge[$this->_offset++];

                return (object)['name' => $C, 'charsetnr' => $this->_charset[$C],];
            }
        }

        class Min_Driver extends
            Min_SQL
        {
            public $hg = "_id";

            public function select($R, $L, $Z, $od, $xf = [], $z = 1, $E = 0, $jg = false)
            {
                $L = ($L == ["*"] ? [] : array_fill_keys($L, true));
                $rh = [];
                foreach ($xf
                         as $X) {
                    $X = preg_replace('~ DESC$~', '', $X, 1, $Eb);
                    $rh[$X] = ($Eb ? -1 : 1);
                }

                return
                    new
                    Min_Result($this->_conn->_db->selectCollection($R)->find([], $L)->sort($rh)->limit($z != "" ? +$z : 0)->skip($E * $z));
            }

            public function insert($R, $O)
            {
                try {
                    $I = $this->_conn->_db->selectCollection($R)->insert($O);
                    $this->_conn->errno = $I['code'];
                    $this->_conn->error = $I['err'];
                    $this->_conn->last_id = $O['_id'];

                    return !$I['err'];
                } catch (Exception$Bc) {
                    $this->_conn->error = $Bc->getMessage();

                    return
                        false;
                }
            }
        }

        function get_databases($bd)
        {
            global $g;
            $I = [];
            $Rb = $g->_link->listDBs();
            foreach ($Rb['databases'] as $l) {
                $I[] = $l['name'];
            }

            return $I;
        }

        function count_tables($k)
        {
            global $g;
            $I = [];
            foreach ($k
                     as $l) {
                $I[$l] = count($g->_link->selectDB($l)->getCollectionNames(true));
            }

            return $I;
        }

        function tables_list()
        {
            global $g;

            return
                array_fill_keys($g->_db->getCollectionNames(true), 'table');
        }

        function drop_databases($k)
        {
            global $g;
            foreach ($k
                     as $l) {
                $Jg = $g->_link->selectDB($l)->drop();
                if (!$Jg['ok']) {
                    return
                    false;
                }
            }

            return
                true;
        }

        function indexes($R, $h = null)
        {
            global $g;
            $I = [];
            foreach ($g->_db->selectCollection($R)->getIndexInfo() as $v) {
                $Yb = [];
                foreach ($v["key"] as $d => $U) {
                    $Yb[] = ($U == -1 ? '1' : null);
                }
                $I[$v["name"]] = ["type" => ($v["name"] == "_id_" ? "PRIMARY" : ($v["unique"] ? "UNIQUE" : "INDEX")), "columns" => array_keys($v["key"]), "lengths" => [], "descs" => $Yb,];
            }

            return $I;
        }

        function fields($R)
        {
            return
                fields_from_edit();
        }

        function found_rows($S, $Z)
        {
            global $g;

            return $g->_db->selectCollection($_GET["select"])->count($Z);
        }

        $sf = ["="];
    } elseif (class_exists('MongoDB\Driver\Manager')) {
        class Min_DB
        {
            public $extension = "MongoDB";

            public $server_info = MONGODB_VERSION;

            public $error;

            public $last_id;

            public $_link;

            public $_db;

            public $_db_name;

            public function connect($Hi, $vf)
            {
                $kb = 'MongoDB\Driver\Manager';

                return
                    new$kb($Hi, $vf);
            }

            public function query($G)
            {
                return
                    false;
            }

            public function select_db($j)
            {
                $this->_db_name = $j;

                return
                    true;
            }

            public function quote($Q)
            {
                return $Q;
            }
        }

        class Min_Result
        {
            public $num_rows;

            public $_rows = [];

            public $_offset = 0;

            public $_charset = [];

            public function __construct($H)
            {
                foreach ($H
                         as $ae) {
                    $J = [];
                    foreach ($ae
                             as $y => $X) {
                        if (is_a($X, 'MongoDB\BSON\Binary')) {
                            $this->_charset[$y] = 63;
                        }
                        $J[$y] = (is_a($X, 'MongoDB\BSON\ObjectID') ? 'MongoDB\BSON\ObjectID("' . strval($X) . '")' : (is_a($X, 'MongoDB\BSON\UTCDatetime') ? $X->toDateTime()->format('Y-m-d H:i:s') : (is_a($X, 'MongoDB\BSON\Binary') ? $X->bin : (is_a($X, 'MongoDB\BSON\Regex') ? strval($X) : (is_object($X) ? json_encode($X, 256) : $X)))));
                    }
                    $this->_rows[] = $J;
                    foreach ($J
                             as $y => $X) {
                        if (!isset($this->_rows[0][$y])) {
                            $this->_rows[0][$y] = null;
                        }
                    }
                }
                $this->num_rows = $H->count;
            }

            public function fetch_assoc()
            {
                $J = current($this->_rows);
                if (!$J) {
                    return $J;
                }
                $I = [];
                foreach ($this->_rows[0] as $y => $X) {
                    $I[$y] = $J[$y];
                }
                next($this->_rows);

                return $I;
            }

            public function fetch_row()
            {
                $I = $this->fetch_assoc();
                if (!$I) {
                    return $I;
                }

                return
                    array_values($I);
            }

            public function fetch_field()
            {
                $ge = array_keys($this->_rows[0]);
                $C = $ge[$this->_offset++];

                return (object)['name' => $C, 'charsetnr' => $this->_charset[$C],];
            }
        }

        class Min_Driver extends
            Min_SQL
        {
            public $hg = "_id";

            public function select($R, $L, $Z, $od, $xf = [], $z = 1, $E = 0, $jg = false)
            {
                global $g;
                $L = ($L == ["*"] ? [] : array_fill_keys($L, 1));
                if (count($L) && !isset($L['_id'])) {
                    $L['_id'] = 0;
                }
                $Z = where_to_query($Z);
                $rh = [];
                foreach ($xf
                         as $X) {
                    $X = preg_replace('~ DESC$~', '', $X, 1, $Eb);
                    $rh[$X] = ($Eb ? -1 : 1);
                }
                if (isset($_GET['limit']) && is_numeric($_GET['limit']) && $_GET['limit'] > 0) {
                    $z = $_GET['limit'];
                }
                $z = min(200, max(1, (int)$z));
                $oh = $E * $z;
                $kb = 'MongoDB\Driver\Query';
                $G = new$kb($Z, ['projection' => $L, 'limit' => $z, 'skip' => $oh, 'sort' => $rh]);
                $Mg = $g->_link->executeQuery("$g->_db_name.$R", $G);

                return
                    new
                    Min_Result($Mg);
            }

            public function update($R, $O, $tg, $z = 0, $M = "\n")
            {
                global $g;
                $l = $g->_db_name;
                $Z = sql_query_where_parser($tg);
                $kb = 'MongoDB\Driver\BulkWrite';
                $Za = new$kb([]);
                if (isset($O['_id'])) {
                    unset($O['_id']);
                }
                $Gg = [];
                foreach ($O
                         as $y => $Y) {
                    if ($Y == 'NULL') {
                        $Gg[$y] = 1;
                        unset($O[$y]);
                    }
                }
                $Gi = ['$set' => $O];
                if (count($Gg)) {
                    $Gi['$unset'] = $Gg;
                }
                $Za->update($Z, $Gi, ['upsert' => false]);
                $Mg = $g->_link->executeBulkWrite("$l.$R", $Za);
                $g->affected_rows = $Mg->getModifiedCount();

                return
                    true;
            }

            public function delete($R, $tg, $z = 0)
            {
                global $g;
                $l = $g->_db_name;
                $Z = sql_query_where_parser($tg);
                $kb = 'MongoDB\Driver\BulkWrite';
                $Za = new$kb([]);
                $Za->delete($Z, ['limit' => $z]);
                $Mg = $g->_link->executeBulkWrite("$l.$R", $Za);
                $g->affected_rows = $Mg->getDeletedCount();

                return
                    true;
            }

            public function insert($R, $O)
            {
                global $g;
                $l = $g->_db_name;
                $kb = 'MongoDB\Driver\BulkWrite';
                $Za = new$kb([]);
                if (isset($O['_id']) && empty($O['_id'])) {
                    unset($O['_id']);
                }
                $Za->insert($O);
                $Mg = $g->_link->executeBulkWrite("$l.$R", $Za);
                $g->affected_rows = $Mg->getInsertedCount();

                return
                    true;
            }
        }

        function get_databases($bd)
        {
            global $g;
            $I = [];
            $kb = 'MongoDB\Driver\Command';
            $tb = new$kb(['listDatabases' => 1]);
            $Mg = $g->_link->executeCommand('admin', $tb);
            foreach ($Mg
                     as $Rb) {
                foreach ($Rb->databases
                         as $l) {
                    $I[] = $l->name;
                }
            }

            return $I;
        }

        function count_tables($k)
        {
            $I = [];

            return $I;
        }

        function tables_list()
        {
            global $g;
            $kb = 'MongoDB\Driver\Command';
            $tb = new$kb(['listCollections' => 1]);
            $Mg = $g->_link->executeCommand($g->_db_name, $tb);
            $rb = [];
            foreach ($Mg
                     as $H) {
                $rb[$H->name] = 'table';
            }

            return $rb;
        }

        function drop_databases($k)
        {
            return
                false;
        }

        function indexes($R, $h = null)
        {
            global $g;
            $I = [];
            $kb = 'MongoDB\Driver\Command';
            $tb = new$kb(['listIndexes' => $R]);
            $Mg = $g->_link->executeCommand($g->_db_name, $tb);
            foreach ($Mg
                     as $v) {
                $Yb = [];
                $e = [];
                foreach (get_object_vars($v->key) as $d => $U) {
                    $Yb[] = ($U == -1 ? '1' : null);
                    $e[] = $d;
                }
                $I[$v->name] = ["type" => ($v->name == "_id_" ? "PRIMARY" : (isset($v->unique) ? "UNIQUE" : "INDEX")), "columns" => $e, "lengths" => [], "descs" => $Yb,];
            }

            return $I;
        }

        function fields($R)
        {
            $p = fields_from_edit();
            if (!count($p)) {
                global $m;
                $H = $m->select($R, ["*"], null, null, [], 10);
                while ($J = $H->fetch_assoc()) {
                    foreach ($J
                             as $y => $X) {
                        $J[$y] = null;
                        $p[$y] = ["field" => $y, "type" => "string", "null" => ($y != $m->primary), "auto_increment" => ($y == $m->primary), "privileges" => ["insert" => 1, "select" => 1, "update" => 1,],];
                    }
                }
            }

            return $p;
        }

        function found_rows($S, $Z)
        {
            global $g;
            $Z = where_to_query($Z);
            $kb = 'MongoDB\Driver\Command';
            $tb = new$kb(['count' => $S['Name'], 'query' => $Z]);
            $Mg = $g->_link->executeCommand($g->_db_name, $tb);
            $ji = $Mg->toArray();

            return $ji[0]->n;
        }

        function sql_query_where_parser($tg)
        {
            $tg = trim(preg_replace('/WHERE[\s]?[(]?\(?/', '', $tg));
            $tg = preg_replace('/\)\)\)$/', ')', $tg);
            $gj = explode(' AND ', $tg);
            $hj = explode(') OR (', $tg);
            $Z = [];
            foreach ($gj
                     as $ej) {
                $Z[] = trim($ej);
            }
            if (count($hj) == 1) {
                $hj = [];
            } elseif (count($hj) > 1) {
                $Z = [];
            }

            return
                where_to_query($Z, $hj);
        }

        function where_to_query($cj = [], $dj = [])
        {
            global $b;
            $Mb = [];
            foreach (['and' => $cj, 'or' => $dj] as $U => $Z) {
                if (is_array($Z)) {
                    foreach ($Z
                             as $Ic) {
                        list($nb, $qf, $X) = explode(" ", $Ic, 3);
                        if ($nb == "_id") {
                            $X = str_replace('MongoDB\BSON\ObjectID("', "", $X);
                            $X = str_replace('")', "", $X);
                            $kb = 'MongoDB\BSON\ObjectID';
                            $X = new$kb($X);
                        }
                        if (!in_array($qf, $b->operators)) {
                            continue;
                        }
                        if (preg_match('~^\(f\)(.+)~', $qf, $B)) {
                            $X = (float)$X;
                            $qf = $B[1];
                        } elseif (preg_match('~^\(date\)(.+)~', $qf, $B)) {
                            $Ob = new
                            DateTime($X);
                            $kb = 'MongoDB\BSON\UTCDatetime';
                            $X = new$kb($Ob->getTimestamp() * 1000);
                            $qf = $B[1];
                        }
                        switch ($qf) {
                            case'=':
                                $qf = '$eq';
                                break;
                            case'!=':
                                $qf = '$ne';
                                break;
                            case'>':
                                $qf = '$gt';
                                break;
                            case'<':
                                $qf = '$lt';
                                break;
                            case'>=':
                                $qf = '$gte';
                                break;
                            case'<=':
                                $qf = '$lte';
                                break;
                            case'regex':
                                $qf = '$regex';
                                break;
                            default:
                                continue;
                        }
                        if ($U == 'and') {
                            $Mb['$and'][] = [$nb => [$qf => $X]];
                        } elseif ($U == 'or') {
                            $Mb['$or'][] = [$nb => [$qf => $X]];
                        }
                    }
                }
            }

            return $Mb;
        }

        $sf = ["=", "!=", ">", "<", ">=", "<=", "regex", "(f)=", "(f)!=", "(f)>", "(f)<", "(f)>=", "(f)<=", "(date)=", "(date)!=", "(date)>", "(date)<", "(date)>=", "(date)<=",];
    }
    function table($u)
    {
        return $u;
    }

    function idf_escape($u)
    {
        return $u;
    }

    function table_status($C = "", $Pc = false)
    {
        $I = [];
        foreach (tables_list() as $R => $U) {
            $I[$R] = ["Name" => $R];
            if ($C == $R) {
                return $I[$R];
            }
        }

        return $I;
    }

    function create_database($l, $pb)
    {
        return
            true;
    }

    function last_id()
    {
        global $g;

        return $g->last_id;
    }

    function error()
    {
        global $g;

        return
            h($g->error);
    }

    function collations()
    {
        return
            [];
    }

    function logged_user()
    {
        global $b;
        $Hb = $b->credentials();

        return $Hb[1];
    }

    function connect()
    {
        global $b;
        $g = new
        Min_DB;
        list($N, $V, $F) = $b->credentials();
        $vf = [];
        if ($V . $F != "") {
            $vf["username"] = $V;
            $vf["password"] = $F;
        }
        $l = $b->database();
        if ($l != "") {
            $vf["db"] = $l;
        }
        try {
            $g->_link = $g->connect("mongodb://$N", $vf);
            if ($F != "") {
                $vf["password"] = "";
                try {
                    $g->connect("mongodb://$N", $vf);

                    return
                        lang(22);
                } catch (Exception$Bc) {
                }
            }

            return $g;
        } catch (Exception$Bc) {
            return $Bc->getMessage();
        }
    }

    function alter_indexes($R, $c)
    {
        global $g;
        foreach ($c
                 as $X) {
            list($U, $C, $O) = $X;
            if ($O == "DROP") {
                $I = $g->_db->command(["deleteIndexes" => $R, "index" => $C]);
            } else {
                $e = [];
                foreach ($O
                         as $d) {
                    $d = preg_replace('~ DESC$~', '', $d, 1, $Eb);
                    $e[$d] = ($Eb ? -1 : 1);
                }
                $I = $g->_db->selectCollection($R)->ensureIndex($e, ["unique" => ($U == "UNIQUE"), "name" => $C,]);
            }
            if ($I['errmsg']) {
                $g->error = $I['errmsg'];

                return
                    false;
            }
        }

        return
            true;
    }

    function support($Qc)
    {
        return
            preg_match("~database|indexes~", $Qc);
    }

    function db_collation($l, $qb)
    {
    }

    function information_schema()
    {
    }

    function is_view($S)
    {
    }

    function convert_field($o)
    {
    }

    function unconvert_field($o, $I)
    {
        return $I;
    }

    function foreign_keys($R)
    {
        return
            [];
    }

    function fk_support($S)
    {
    }

    function engines()
    {
        return
            [];
    }

    function alter_table($R, $C, $p, $dd, $vb, $vc, $pb, $Ma, $Rf)
    {
        global $g;
        if ($R == "") {
            $g->_db->createCollection($C);

            return
                true;
        }
    }

    function drop_tables($T)
    {
        global $g;
        foreach ($T
                 as $R) {
            $Jg = $g->_db->selectCollection($R)->drop();
            if (!$Jg['ok']) {
                return
                false;
            }
        }

        return
            true;
    }

    function truncate_tables($T)
    {
        global $g;
        foreach ($T
                 as $R) {
            $Jg = $g->_db->selectCollection($R)->remove();
            if (!$Jg['ok']) {
                return
                false;
            }
        }

        return
            true;
    }

    $x = "mongo";
    $ld = [];
    $rd = [];
    $nc = [["json"]];
}
$fc["elastic"] = "Elasticsearch (beta)";
if (isset($_GET["elastic"])) {
    $eg = ["json + allow_url_fopen"];
    define("DRIVER", "elastic");
    if (function_exists('json_decode') && ini_bool('allow_url_fopen')) {
        class Min_DB
        {
            public $extension = "JSON";

            public $server_info;

            public $errno;

            public $error;

            public $_url;

            public function rootQuery($Vf, $_b = [], $Qe = 'GET')
            {
                @ini_set('track_errors', 1);
                $Uc = @file_get_contents("$this->_url/" . ltrim($Vf, '/'), false, stream_context_create(['http' => ['method' => $Qe, 'content' => $_b === null ? $_b : json_encode($_b), 'header' => 'Content-Type: application/json', 'ignore_errors' => 1,]]));
                if (!$Uc) {
                    $this->error = $php_errormsg;

                    return $Uc;
                }
                if (!preg_match('~^HTTP/[0-9.]+ 2~i', $http_response_header[0])) {
                    $this->error = $Uc;

                    return
                        false;
                }
                $I = json_decode($Uc, true);
                if ($I === null) {
                    $this->errno = json_last_error();
                    if (function_exists('json_last_error_msg')) {
                        $this->error = json_last_error_msg();
                    } else {
                        $zb = get_defined_constants(true);
                        foreach ($zb['json'] as $C => $Y) {
                            if ($Y == $this->errno && preg_match('~^JSON_ERROR_~', $C)) {
                                $this->error = $C;
                                break;
                            }
                        }
                    }
                }

                return $I;
            }

            public function query($Vf, $_b = [], $Qe = 'GET')
            {
                return $this->rootQuery(($this->_db != "" ? "$this->_db/" : "/") . ltrim($Vf, '/'), $_b, $Qe);
            }

            public function connect($N, $V, $F)
            {
                preg_match('~^(https?://)?(.*)~', $N, $B);
                $this->_url = ($B[1] ? $B[1] : "http://") . "$V:$F@$B[2]";
                $I = $this->query('');
                if ($I) {
                    $this->server_info = $I['version']['number'];
                }

                return (bool)$I;
            }

            public function select_db($j)
            {
                $this->_db = $j;

                return
                    true;
            }

            public function quote($Q)
            {
                return $Q;
            }
        }

        class Min_Result
        {
            public $num_rows;

            public $_rows;

            public function __construct($K)
            {
                $this->num_rows = count($this->_rows);
                $this->_rows = $K;
                reset($this->_rows);
            }

            public function fetch_assoc()
            {
                $I = current($this->_rows);
                next($this->_rows);

                return $I;
            }

            public function fetch_row()
            {
                return
                    array_values($this->fetch_assoc());
            }
        }
    }

    class Min_Driver extends
        Min_SQL
    {
        public function select($R, $L, $Z, $od, $xf = [], $z = 1, $E = 0, $jg = false)
        {
            global $b;
            $Mb = [];
            $G = "$R/_search";
            if ($L != ["*"]) {
                $Mb["fields"] = $L;
            }
            if ($xf) {
                $rh = [];
                foreach ($xf
                         as $nb) {
                    $nb = preg_replace('~ DESC$~', '', $nb, 1, $Eb);
                    $rh[] = ($Eb ? [$nb => "desc"] : $nb);
                }
                $Mb["sort"] = $rh;
            }
            if ($z) {
                $Mb["size"] = +$z;
                if ($E) {
                    $Mb["from"] = ($E * $z);
                }
            }
            foreach ($Z
                     as $X) {
                list($nb, $qf, $X) = explode(" ", $X, 3);
                if ($nb == "_id") {
                    $Mb["query"]["ids"]["values"][] = $X;
                } elseif ($nb . $X != "") {
                    $Wh = ["term" => [($nb != "" ? $nb : "_all") => $X]];
                    if ($qf == "=") {
                        $Mb["query"]["filtered"]["filter"]["and"][] = $Wh;
                    } else {
                        $Mb["query"]["filtered"]["query"]["bool"]["must"][] = $Wh;
                    }
                }
            }
            if ($Mb["query"] && !$Mb["query"]["filtered"]["query"] && !$Mb["query"]["ids"]) {
                $Mb["query"]["filtered"]["query"] = ["match_all" => []];
            }
            $_h = microtime(true);
            $Zg = $this->_conn->query($G, $Mb);
            if ($jg) {
                echo $b->selectQuery("$G: " . print_r($Mb, true), $_h, !$Zg);
            }
            if (!$Zg) {
                return
                false;
            }
            $I = [];
            foreach ($Zg['hits']['hits'] as $Ad) {
                $J = [];
                if ($L == ["*"]) {
                    $J["_id"] = $Ad["_id"];
                }
                $p = $Ad['_source'];
                if ($L != ["*"]) {
                    $p = [];
                    foreach ($L
                             as $y) {
                        $p[$y] = $Ad['fields'][$y];
                    }
                }
                foreach ($p
                         as $y => $X) {
                    if ($Mb["fields"]) {
                        $X = $X[0];
                    }
                    $J[$y] = (is_array($X) ? json_encode($X) : $X);
                }
                $I[] = $J;
            }

            return
                new
                Min_Result($I);
        }

        public function update($U, $yg, $tg, $z = 0, $M = "\n")
        {
            $Tf = preg_split('~ *= *~', $tg);
            if (count($Tf) == 2) {
                $t = trim($Tf[1]);
                $G = "$U/$t";

                return $this->_conn->query($G, $yg, 'POST');
            }

            return
                false;
        }

        public function insert($U, $yg)
        {
            $t = "";
            $G = "$U/$t";
            $Jg = $this->_conn->query($G, $yg, 'POST');
            $this->_conn->last_id = $Jg['_id'];

            return $Jg['created'];
        }

        public function delete($U, $tg, $z = 0)
        {
            $Ed = [];
            if (is_array($_GET["where"]) && $_GET["where"]["_id"]) {
                $Ed[] = $_GET["where"]["_id"];
            }
            if (is_array($_POST['check'])) {
                foreach ($_POST['check'] as $db) {
                    $Tf = preg_split('~ *= *~', $db);
                    if (count($Tf) == 2) {
                        $Ed[] = trim($Tf[1]);
                    }
                }
            }
            $this->_conn->affected_rows = 0;
            foreach ($Ed
                     as $t) {
                $G = "{$U}/{$t}";
                $Jg = $this->_conn->query($G, '{}', 'DELETE');
                if (is_array($Jg) && $Jg['found'] == true) {
                    $this->_conn->affected_rows++;
                }
            }

            return $this->_conn->affected_rows;
        }
    }

    function connect()
    {
        global $b;
        $g = new
        Min_DB;
        list($N, $V, $F) = $b->credentials();
        if ($F != "" && $g->connect($N, $V, "")) {
            return
            lang(22);
        }
        if ($g->connect($N, $V, $F)) {
            return $g;
        }

        return $g->error;
    }

    function support($Qc)
    {
        return
            preg_match("~database|table|columns~", $Qc);
    }

    function logged_user()
    {
        global $b;
        $Hb = $b->credentials();

        return $Hb[1];
    }

    function get_databases()
    {
        global $g;
        $I = $g->rootQuery('_aliases');
        if ($I) {
            $I = array_keys($I);
            sort($I, SORT_STRING);
        }

        return $I;
    }

    function collations()
    {
        return
            [];
    }

    function db_collation($l, $qb)
    {
    }

    function engines()
    {
        return
            [];
    }

    function count_tables($k)
    {
        global $g;
        $I = [];
        $H = $g->query('_stats');
        if ($H && $H['indices']) {
            $Ld = $H['indices'];
            foreach ($Ld
                     as $Kd => $Ah) {
                $Jd = $Ah['total']['indexing'];
                $I[$Kd] = $Jd['index_total'];
            }
        }

        return $I;
    }

    function tables_list()
    {
        global $g;
        $I = $g->query('_mapping');
        if ($I) {
            $I = array_fill_keys(array_keys($I[$g->_db]["mappings"]), 'table');
        }

        return $I;
    }

    function table_status($C = "", $Pc = false)
    {
        global $g;
        $Zg = $g->query("_search", ["size" => 0, "aggregations" => ["count_by_type" => ["terms" => ["field" => "_type"]]]], "POST");
        $I = [];
        if ($Zg) {
            $T = $Zg["aggregations"]["count_by_type"]["buckets"];
            foreach ($T
                     as $R) {
                $I[$R["key"]] = ["Name" => $R["key"], "Engine" => "table", "Rows" => $R["doc_count"],];
                if ($C != "" && $C == $R["key"]) {
                    return $I[$C];
                }
            }
        }

        return $I;
    }

    function error()
    {
        global $g;

        return
            h($g->error);
    }

    function information_schema()
    {
    }

    function is_view($S)
    {
    }

    function indexes($R, $h = null)
    {
        return
            [["type" => "PRIMARY", "columns" => ["_id"]],];
    }

    function fields($R)
    {
        global $g;
        $H = $g->query("$R/_mapping");
        $I = [];
        if ($H) {
            $ze = $H[$R]['properties'];
            if (!$ze) {
                $ze = $H[$g->_db]['mappings'][$R]['properties'];
            }
            if ($ze) {
                foreach ($ze
                         as $C => $o) {
                    $I[$C] = ["field" => $C, "full_type" => $o["type"], "type" => $o["type"], "privileges" => ["insert" => 1, "select" => 1, "update" => 1],];
                    if ($o["properties"]) {
                        unset($I[$C]["privileges"]["insert"]);
                        unset($I[$C]["privileges"]["update"]);
                    }
                }
            }
        }

        return $I;
    }

    function foreign_keys($R)
    {
        return
            [];
    }

    function table($u)
    {
        return $u;
    }

    function idf_escape($u)
    {
        return $u;
    }

    function convert_field($o)
    {
    }

    function unconvert_field($o, $I)
    {
        return $I;
    }

    function fk_support($S)
    {
    }

    function found_rows($S, $Z)
    {
        return
            null;
    }

    function create_database($l)
    {
        global $g;

        return $g->rootQuery(urlencode($l), null, 'PUT');
    }

    function drop_databases($k)
    {
        global $g;

        return $g->rootQuery(urlencode(implode(',', $k)), [], 'DELETE');
    }

    function alter_table($R, $C, $p, $dd, $vb, $vc, $pb, $Ma, $Rf)
    {
        global $g;
        $pg = [];
        foreach ($p
                 as $Nc) {
            $Sc = trim($Nc[1][0]);
            $Tc = trim($Nc[1][1] ? $Nc[1][1] : "text");
            $pg[$Sc] = ['type' => $Tc];
        }
        if (!empty($pg)) {
            $pg = ['properties' => $pg];
        }

        return $g->query("_mapping/{$C}", $pg, 'PUT');
    }

    function drop_tables($T)
    {
        global $g;
        $I = true;
        foreach ($T
                 as $R) {
            $I = $I && $g->query(urlencode($R), [], 'DELETE');
        }

        return $I;
    }

    function last_id()
    {
        global $g;

        return $g->last_id;
    }

    $x = "elastic";
    $sf = ["=", "query"];
    $ld = [];
    $rd = [];
    $nc = [["json"]];
    $zi = [];
    $Dh = [];
    foreach ([lang(27) => ["long" => 3, "integer" => 5, "short" => 8, "byte" => 10, "double" => 20, "float" => 66, "half_float" => 12, "scaled_float" => 21], lang(28) => ["date" => 10], lang(25) => ["string" => 65535, "text" => 65535], lang(29) => ["binary" => 255],] as $y => $X) {
        $zi += $X;
        $Dh[$y] = array_keys($X);
    }
}
$fc = ["server" => "MySQL"] + $fc;
if (!defined("DRIVER")) {
    $eg = ["MySQLi", "MySQL", "PDO_MySQL"];
    define("DRIVER", "server");
    if (extension_loaded("mysqli")) {
        class Min_DB extends
            MySQLi
        {
            public $extension = "MySQLi";

            public function __construct()
            {
                parent::init();
            }

            public function connect($N = "", $V = "", $F = "", $j = null, $ag = null, $qh = null)
            {
                global $b;
                mysqli_report(MYSQLI_REPORT_OFF);
                list($Bd, $ag) = explode(":", $N, 2);
                $zh = $b->connectSsl();
                if ($zh) {
                    $this->ssl_set($zh['key'], $zh['cert'], $zh['ca'], '', '');
                }
                $I = @$this->real_connect(($N != "" ? $Bd : ini_get("mysqli.default_host")), ($N . $V != "" ? $V : ini_get("mysqli.default_user")), ($N . $V . $F != "" ? $F : ini_get("mysqli.default_pw")), $j, (is_numeric($ag) ? $ag : ini_get("mysqli.default_port")), (!is_numeric($ag) ? $ag : $qh), ($zh ? 64 : 0));
                $this->options(MYSQLI_OPT_LOCAL_INFILE, false);

                return $I;
            }

            public function set_charset($cb)
            {
                if (parent::set_charset($cb)) {
                    return
                    true;
                }
                parent::set_charset('utf8');

                return $this->query("SET NAMES $cb");
            }

            public function result($G, $o = 0)
            {
                $H = $this->query($G);
                if (!$H) {
                    return
                    false;
                }
                $J = $H->fetch_array();

                return $J[$o];
            }

            public function quote($Q)
            {
                return "'" . $this->escape_string($Q) . "'";
            }
        }
    } elseif (extension_loaded("mysql") && !((ini_bool("sql.safe_mode") || ini_bool("mysql.allow_local_infile")) && extension_loaded("pdo_mysql"))) {
        class Min_DB
        {
            public $extension = "MySQL";

            public $server_info;

            public $affected_rows;

            public $errno;

            public $error;

            public $_link;

            public $_result;

            public function connect($N, $V, $F)
            {
                if (ini_bool("mysql.allow_local_infile")) {
                    $this->error = lang(32, "'mysql.allow_local_infile'", "MySQLi", "PDO_MySQL");

                    return
                        false;
                }
                $this->_link = @mysql_connect(($N != "" ? $N : ini_get("mysql.default_host")), ("$N$V" != "" ? $V : ini_get("mysql.default_user")), ("$N$V$F" != "" ? $F : ini_get("mysql.default_password")), true, 131072);
                if ($this->_link) {
                    $this->server_info = mysql_get_server_info($this->_link);
                } else {
                    $this->error = mysql_error();
                }

                return (bool)$this->_link;
            }

            public function set_charset($cb)
            {
                if (function_exists('mysql_set_charset')) {
                    if (mysql_set_charset($cb, $this->_link)) {
                        return
                        true;
                    }
                    mysql_set_charset('utf8', $this->_link);
                }

                return $this->query("SET NAMES $cb");
            }

            public function quote($Q)
            {
                return "'" . mysql_real_escape_string($Q, $this->_link) . "'";
            }

            public function select_db($j)
            {
                return
                    mysql_select_db($j, $this->_link);
            }

            public function query($G, $_i = false)
            {
                $H = @($_i ? mysql_unbuffered_query($G, $this->_link) : mysql_query($G, $this->_link));
                $this->error = "";
                if (!$H) {
                    $this->errno = mysql_errno($this->_link);
                    $this->error = mysql_error($this->_link);

                    return
                        false;
                }
                if ($H === true) {
                    $this->affected_rows = mysql_affected_rows($this->_link);
                    $this->info = mysql_info($this->_link);

                    return
                        true;
                }

                return
                    new
                    Min_Result($H);
            }

            public function multi_query($G)
            {
                return $this->_result = $this->query($G);
            }

            public function store_result()
            {
                return $this->_result;
            }

            public function next_result()
            {
                return
                    false;
            }

            public function result($G, $o = 0)
            {
                $H = $this->query($G);
                if (!$H || !$H->num_rows) {
                    return
                    false;
                }

                return
                    mysql_result($H->_result, 0, $o);
            }
        }

        class Min_Result
        {
            public $num_rows;

            public $_result;

            public $_offset = 0;

            public function __construct($H)
            {
                $this->_result = $H;
                $this->num_rows = mysql_num_rows($H);
            }

            public function fetch_assoc()
            {
                return
                    mysql_fetch_assoc($this->_result);
            }

            public function fetch_row()
            {
                return
                    mysql_fetch_row($this->_result);
            }

            public function fetch_field()
            {
                $I = mysql_fetch_field($this->_result, $this->_offset++);
                $I->orgtable = $I->table;
                $I->orgname = $I->name;
                $I->charsetnr = ($I->blob ? 63 : 0);

                return $I;
            }

            public function __destruct()
            {
                mysql_free_result($this->_result);
            }
        }
    } elseif (extension_loaded("pdo_mysql")) {
        class Min_DB extends
            Min_PDO
        {
            public $extension = "PDO_MySQL";

            public function connect($N, $V, $F)
            {
                global $b;
                $vf = [PDO::MYSQL_ATTR_LOCAL_INFILE => false];
                $zh = $b->connectSsl();
                if ($zh) {
                    $vf += [PDO::MYSQL_ATTR_SSL_KEY => $zh['key'], PDO::MYSQL_ATTR_SSL_CERT => $zh['cert'], PDO::MYSQL_ATTR_SSL_CA => $zh['ca'],];
                }
                $this->dsn("mysql:charset=utf8;host=" . str_replace(":", ";unix_socket=", preg_replace('~:(\d)~', ';port=\1', $N)), $V, $F, $vf);

                return
                    true;
            }

            public function set_charset($cb)
            {
                $this->query("SET NAMES $cb");
            }

            public function select_db($j)
            {
                return $this->query("USE " . idf_escape($j));
            }

            public function query($G, $_i = false)
            {
                $this->setAttribute(1000, !$_i);

                return
                    parent::query($G, $_i);
            }
        }
    }

    class Min_Driver extends
        Min_SQL
    {
        public function insert($R, $O)
        {
            return ($O ? parent::insert($R, $O) : queries("INSERT INTO " . table($R) . " ()\nVALUES ()"));
        }

        public function insertUpdate($R, $K, $hg)
        {
            $e = array_keys(reset($K));
            $fg = "INSERT INTO " . table($R) . " (" . implode(", ", $e) . ") VALUES\n";
            $Ri = [];
            foreach ($e
                     as $y) {
                $Ri[$y] = "$y = VALUES($y)";
            }
            $Gh = "\nON DUPLICATE KEY UPDATE " . implode(", ", $Ri);
            $Ri = [];
            $te = 0;
            foreach ($K
                     as $O) {
                $Y = "(" . implode(", ", $O) . ")";
                if ($Ri && (strlen($fg) + $te + strlen($Y) + strlen($Gh) > 1e6)) {
                    if (!queries($fg . implode(",\n", $Ri) . $Gh)) {
                        return
                        false;
                    }
                    $Ri = [];
                    $te = 0;
                }
                $Ri[] = $Y;
                $te += strlen($Y) + 2;
            }

            return
                queries($fg . implode(",\n", $Ri) . $Gh);
        }

        public function slowQuery($G, $bi)
        {
            if (min_version('5.7.8', '10.1.2')) {
                if (preg_match('~MariaDB~', $this->_conn->server_info)) {
                    return "SET STATEMENT max_statement_time=$bi FOR $G";
                } elseif (preg_match('~^(SELECT\b)(.+)~is', $G, $B)) {
                    return "$B[1] /*+ MAX_EXECUTION_TIME(" . ($bi * 1000) . ") */ $B[2]";
                }
            }
        }

        public function convertSearch($u, $X, $o)
        {
            return (preg_match('~char|text|enum|set~', $o["type"]) && !preg_match("~^utf8~", $o["collation"]) && preg_match('~[\x80-\xFF]~', $X['val']) ? "CONVERT($u USING " . charset($this->_conn) . ")" : $u);
        }

        public function warnings()
        {
            $H = $this->_conn->query("SHOW WARNINGS");
            if ($H && $H->num_rows) {
                ob_start();
                select($H);

                return
                    ob_get_clean();
            }
        }

        public function tableHelp($C)
        {
            $_e = preg_match('~MariaDB~', $this->_conn->server_info);
            if (information_schema(DB)) {
                return
                strtolower(($_e ? "information-schema-$C-table/" : str_replace("_", "-", $C) . "-table.html"));
            }
            if (DB == "mysql") {
                return ($_e ? "mysql$C-table/" : "system-database.html");
            }
        }
    }

    function idf_escape($u)
    {
        return "`" . str_replace("`", "``", $u) . "`";
    }

    function table($u)
    {
        return
            idf_escape($u);
    }

    function connect()
    {
        global $b, $zi, $Dh;
        $g = new
        Min_DB;
        $Hb = $b->credentials();
        if ($g->connect($Hb[0], $Hb[1], $Hb[2])) {
            $g->set_charset(charset($g));
            $g->query("SET sql_quote_show_create = 1, autocommit = 1");
            if (min_version('5.7.8', 10.2, $g)) {
                $Dh[lang(25)][] = "json";
                $zi["json"] = 4294967295;
            }

            return $g;
        }
        $I = $g->error;
        if (function_exists('iconv') && !is_utf8($I) && strlen($Vg = iconv("windows-1250", "utf-8", $I)) > strlen($I)) {
            $I = $Vg;
        }

        return $I;
    }

    function get_databases($bd)
    {
        $I = get_session("dbs");
        if ($I === null) {
            $G = (min_version(5) ? "SELECT SCHEMA_NAME FROM information_schema.SCHEMATA ORDER BY SCHEMA_NAME" : "SHOW DATABASES");
            $I = ($bd ? slow_query($G) : get_vals($G));
            restart_session();
            set_session("dbs", $I);
            stop_session();
        }

        return $I;
    }

    function limit($G, $Z, $z, $D = 0, $M = " ")
    {
        return " $G$Z" . ($z !== null ? $M . "LIMIT $z" . ($D ? " OFFSET $D" : "") : "");
    }

    function limit1($R, $G, $Z, $M = "\n")
    {
        return
            limit($G, $Z, 1, 0, $M);
    }

    function db_collation($l, $qb)
    {
        global $g;
        $I = null;
        $i = $g->result("SHOW CREATE DATABASE " . idf_escape($l), 1);
        if (preg_match('~ COLLATE ([^ ]+)~', $i, $B)) {
            $I = $B[1];
        } elseif (preg_match('~ CHARACTER SET ([^ ]+)~', $i, $B)) {
            $I = $qb[$B[1]][-1];
        }

        return $I;
    }

    function engines()
    {
        $I = [];
        foreach (get_rows("SHOW ENGINES") as $J) {
            if (preg_match("~YES|DEFAULT~", $J["Support"])) {
                $I[] = $J["Engine"];
            }
        }

        return $I;
    }

    function logged_user()
    {
        global $g;

        return $g->result("SELECT USER()");
    }

    function tables_list()
    {
        return
            get_key_vals(min_version(5) ? "SELECT TABLE_NAME, TABLE_TYPE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ORDER BY TABLE_NAME" : "SHOW TABLES");
    }

    function count_tables($k)
    {
        $I = [];
        foreach ($k
                 as $l) {
            $I[$l] = count(get_vals("SHOW TABLES IN " . idf_escape($l)));
        }

        return $I;
    }

    function table_status($C = "", $Pc = false)
    {
        $I = [];
        foreach (get_rows($Pc && min_version(5) ? "SELECT TABLE_NAME AS Name, ENGINE AS Engine, TABLE_COMMENT AS Comment FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() " . ($C != "" ? "AND TABLE_NAME = " . q($C) : "ORDER BY Name") : "SHOW TABLE STATUS" . ($C != "" ? " LIKE " . q(addcslashes($C, "%_\\")) : "")) as $J) {
            if ($J["Engine"] == "InnoDB") {
                $J["Comment"] = preg_replace('~(?:(.+); )?InnoDB free: .*~', '\1', $J["Comment"]);
            }
            if (!isset($J["Engine"])) {
                $J["Comment"] = "";
            }
            if ($C != "") {
                return $J;
            }
            $I[$J["Name"]] = $J;
        }

        return $I;
    }

    function is_view($S)
    {
        return $S["Engine"] === null;
    }

    function fk_support($S)
    {
        return
            preg_match('~InnoDB|IBMDB2I~i', $S["Engine"]) || (preg_match('~NDB~i', $S["Engine"]) && min_version(5.6));
    }

    function fields($R)
    {
        $I = [];
        foreach (get_rows("SHOW FULL COLUMNS FROM " . table($R)) as $J) {
            preg_match('~^([^( ]+)(?:\((.+)\))?( unsigned)?( zerofill)?$~', $J["Type"], $B);
            $I[$J["Field"]] = ["field" => $J["Field"], "full_type" => $J["Type"], "type" => $B[1], "length" => $B[2], "unsigned" => ltrim($B[3] . $B[4]), "default" => ($J["Default"] != "" || preg_match("~char|set~", $B[1]) ? $J["Default"] : null), "null" => ($J["Null"] == "YES"), "auto_increment" => ($J["Extra"] == "auto_increment"), "on_update" => (preg_match('~^on update (.+)~i', $J["Extra"], $B) ? $B[1] : ""), "collation" => $J["Collation"], "privileges" => array_flip(preg_split('~, *~', $J["Privileges"])), "comment" => $J["Comment"], "primary" => ($J["Key"] == "PRI"),];
        }

        return $I;
    }

    function indexes($R, $h = null)
    {
        $I = [];
        foreach (get_rows("SHOW INDEX FROM " . table($R), $h) as $J) {
            $C = $J["Key_name"];
            $I[$C]["type"] = ($C == "PRIMARY" ? "PRIMARY" : ($J["Index_type"] == "FULLTEXT" ? "FULLTEXT" : ($J["Non_unique"] ? ($J["Index_type"] == "SPATIAL" ? "SPATIAL" : "INDEX") : "UNIQUE")));
            $I[$C]["columns"][] = $J["Column_name"];
            $I[$C]["lengths"][] = ($J["Index_type"] == "SPATIAL" ? null : $J["Sub_part"]);
            $I[$C]["descs"][] = null;
        }

        return $I;
    }

    function foreign_keys($R)
    {
        global $g, $nf;
        static $Xf = '`(?:[^`]|``)+`';
        $I = [];
        $Fb = $g->result("SHOW CREATE TABLE " . table($R), 1);
        if ($Fb) {
            preg_match_all("~CONSTRAINT ($Xf) FOREIGN KEY ?\\(((?:$Xf,? ?)+)\\) REFERENCES ($Xf)(?:\\.($Xf))? \\(((?:$Xf,? ?)+)\\)(?: ON DELETE ($nf))?(?: ON UPDATE ($nf))?~", $Fb, $Ce, PREG_SET_ORDER);
            foreach ($Ce
                     as $B) {
                preg_match_all("~$Xf~", $B[2], $sh);
                preg_match_all("~$Xf~", $B[5], $Th);
                $I[idf_unescape($B[1])] = ["db" => idf_unescape($B[4] != "" ? $B[3] : $B[4]), "table" => idf_unescape($B[4] != "" ? $B[4] : $B[3]), "source" => array_map('idf_unescape', $sh[0]), "target" => array_map('idf_unescape', $Th[0]), "on_delete" => ($B[6] ? $B[6] : "RESTRICT"), "on_update" => ($B[7] ? $B[7] : "RESTRICT"),];
            }
        }

        return $I;
    }

    function view($C)
    {
        global $g;

        return
            ["select" => preg_replace('~^(?:[^`]|`[^`]*`)*\s+AS\s+~isU', '', $g->result("SHOW CREATE VIEW " . table($C), 1))];
    }

    function collations()
    {
        $I = [];
        foreach (get_rows("SHOW COLLATION") as $J) {
            if ($J["Default"]) {
                $I[$J["Charset"]][-1] = $J["Collation"];
            } else {
                $I[$J["Charset"]][] = $J["Collation"];
            }
        }
        ksort($I);
        foreach ($I
                 as $y => $X) {
            asort($I[$y]);
        }

        return $I;
    }

    function information_schema($l)
    {
        return (min_version(5) && $l == "information_schema") || (min_version(5.5) && $l == "performance_schema");
    }

    function error()
    {
        global $g;

        return
            h(preg_replace('~^You have an error.*syntax to use~U', "Syntax error", $g->error));
    }

    function create_database($l, $pb)
    {
        return
            queries("CREATE DATABASE " . idf_escape($l) . ($pb ? " COLLATE " . q($pb) : ""));
    }

    function drop_databases($k)
    {
        $I = apply_queries("DROP DATABASE", $k, 'idf_escape');
        restart_session();
        set_session("dbs", null);

        return $I;
    }

    function rename_database($C, $pb)
    {
        $I = false;
        if (create_database($C, $pb)) {
            $Hg = [];
            foreach (tables_list() as $R => $U) {
                $Hg[] = table($R) . " TO " . idf_escape($C) . "." . table($R);
            }
            $I = (!$Hg || queries("RENAME TABLE " . implode(", ", $Hg)));
            if ($I) {
                queries("DROP DATABASE " . idf_escape(DB));
            }
            restart_session();
            set_session("dbs", null);
        }

        return $I;
    }

    function auto_increment()
    {
        $Na = " PRIMARY KEY";
        if ($_GET["create"] != "" && $_POST["auto_increment_col"]) {
            foreach (indexes($_GET["create"]) as $v) {
                if (in_array($_POST["fields"][$_POST["auto_increment_col"]]["orig"], $v["columns"], true)) {
                    $Na = "";
                    break;
                }
                if ($v["type"] == "PRIMARY") {
                    $Na = " UNIQUE";
                }
            }
        }

        return " AUTO_INCREMENT$Na";
    }

    function alter_table($R, $C, $p, $dd, $vb, $vc, $pb, $Ma, $Rf)
    {
        $c = [];
        foreach ($p
                 as $o) {
            $c[] = ($o[1] ? ($R != "" ? ($o[0] != "" ? "CHANGE " . idf_escape($o[0]) : "ADD") : " ") . " " . implode($o[1]) . ($R != "" ? $o[2] : "") : "DROP " . idf_escape($o[0]));
        }
        $c = array_merge($c, $dd);
        $P = ($vb !== null ? " COMMENT=" . q($vb) : "") . ($vc ? " ENGINE=" . q($vc) : "") . ($pb ? " COLLATE " . q($pb) : "") . ($Ma != "" ? " AUTO_INCREMENT=$Ma" : "");
        if ($R == "") {
            return
            queries("CREATE TABLE " . table($C) . " (\n" . implode(",\n", $c) . "\n)$P$Rf");
        }
        if ($R != $C) {
            $c[] = "RENAME TO " . table($C);
        }
        if ($P) {
            $c[] = ltrim($P);
        }

        return ($c || $Rf ? queries("ALTER TABLE " . table($R) . "\n" . implode(",\n", $c) . $Rf) : true);
    }

    function alter_indexes($R, $c)
    {
        foreach ($c
                 as $y => $X) {
            $c[$y] = ($X[2] == "DROP" ? "\nDROP INDEX " . idf_escape($X[1]) : "\nADD $X[0] " . ($X[0] == "PRIMARY" ? "KEY " : "") . ($X[1] != "" ? idf_escape($X[1]) . " " : "") . "(" . implode(", ", $X[2]) . ")");
        }

        return
            queries("ALTER TABLE " . table($R) . implode(",", $c));
    }

    function truncate_tables($T)
    {
        return
            apply_queries("TRUNCATE TABLE", $T);
    }

    function drop_views($Wi)
    {
        return
            queries("DROP VIEW " . implode(", ", array_map('table', $Wi)));
    }

    function drop_tables($T)
    {
        return
            queries("DROP TABLE " . implode(", ", array_map('table', $T)));
    }

    function move_tables($T, $Wi, $Th)
    {
        $Hg = [];
        foreach (array_merge($T, $Wi) as $R) {
            $Hg[] = table($R) . " TO " . idf_escape($Th) . "." . table($R);
        }

        return
            queries("RENAME TABLE " . implode(", ", $Hg));
    }

    function copy_tables($T, $Wi, $Th)
    {
        queries("SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO'");
        foreach ($T
                 as $R) {
            $C = ($Th == DB ? table("copy_$R") : idf_escape($Th) . "." . table($R));
            if (!queries("\nDROP TABLE IF EXISTS $C") || !queries("CREATE TABLE $C LIKE " . table($R)) || !queries("INSERT INTO $C SELECT * FROM " . table($R))) {
                return
                false;
            }
            foreach (get_rows("SHOW TRIGGERS LIKE " . q(addcslashes($R, "%_\\"))) as $J) {
                $ti = $J["Trigger"];
                if (!queries("CREATE TRIGGER " . ($Th == DB ? idf_escape("copy_$ti") : idf_escape($Th) . "." . idf_escape($ti)) . " $J[Timing] $J[Event] ON $C FOR EACH ROW\n$J[Statement];")) {
                    return
                    false;
                }
            }
        }
        foreach ($Wi
                 as $R) {
            $C = ($Th == DB ? table("copy_$R") : idf_escape($Th) . "." . table($R));
            $Vi = view($R);
            if (!queries("DROP VIEW IF EXISTS $C") || !queries("CREATE VIEW $C AS $Vi[select]")) {
                return
                false;
            }
        }

        return
            true;
    }

    function trigger($C)
    {
        if ($C == "") {
            return
            [];
        }
        $K = get_rows("SHOW TRIGGERS WHERE `Trigger` = " . q($C));

        return
            reset($K);
    }

    function triggers($R)
    {
        $I = [];
        foreach (get_rows("SHOW TRIGGERS LIKE " . q(addcslashes($R, "%_\\"))) as $J) {
            $I[$J["Trigger"]] = [$J["Timing"], $J["Event"]];
        }

        return $I;
    }

    function trigger_options()
    {
        return
            ["Timing" => ["BEFORE", "AFTER"], "Event" => ["INSERT", "UPDATE", "DELETE"], "Type" => ["FOR EACH ROW"],];
    }

    function routine($C, $U)
    {
        global $g, $xc, $Qd, $zi;
        $Da = ["bool", "boolean", "integer", "double precision", "real", "dec", "numeric", "fixed", "national char", "national varchar"];
        $th = "(?:\\s|/\\*[\s\S]*?\\*/|(?:#|-- )[^\n]*\n?|--\r?\n)";
        $yi = "((" . implode("|", array_merge(array_keys($zi), $Da)) . ")\\b(?:\\s*\\(((?:[^'\")]|$xc)++)\\))?\\s*(zerofill\\s*)?(unsigned(?:\\s+zerofill)?)?)(?:\\s*(?:CHARSET|CHARACTER\\s+SET)\\s*['\"]?([^'\"\\s,]+)['\"]?)?";
        $Xf = "$th*(" . ($U == "FUNCTION" ? "" : $Qd) . ")?\\s*(?:`((?:[^`]|``)*)`\\s*|\\b(\\S+)\\s+)$yi";
        $i = $g->result("SHOW CREATE $U " . idf_escape($C), 2);
        preg_match("~\\(((?:$Xf\\s*,?)*)\\)\\s*" . ($U == "FUNCTION" ? "RETURNS\\s+$yi\\s+" : "") . "(.*)~is", $i, $B);
        $p = [];
        preg_match_all("~$Xf\\s*,?~is", $B[1], $Ce, PREG_SET_ORDER);
        foreach ($Ce
                 as $Kf) {
            $C = str_replace("``", "`", $Kf[2]) . $Kf[3];
            $p[] = ["field" => $C, "type" => strtolower($Kf[5]), "length" => preg_replace_callback("~$xc~s", 'normalize_enum', $Kf[6]), "unsigned" => strtolower(preg_replace('~\s+~', ' ', trim("$Kf[8] $Kf[7]"))), "null" => 1, "full_type" => $Kf[4], "inout" => strtoupper($Kf[1]), "collation" => strtolower($Kf[9]),];
        }
        if ($U != "FUNCTION") {
            return
            ["fields" => $p, "definition" => $B[11]];
        }

        return
            ["fields" => $p, "returns" => ["type" => $B[12], "length" => $B[13], "unsigned" => $B[15], "collation" => $B[16]], "definition" => $B[17], "language" => "SQL",];
    }

    function routines()
    {
        return
            get_rows("SELECT ROUTINE_NAME AS SPECIFIC_NAME, ROUTINE_NAME, ROUTINE_TYPE, DTD_IDENTIFIER FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = " . q(DB));
    }

    function routine_languages()
    {
        return
            [];
    }

    function routine_id($C, $J)
    {
        return
            idf_escape($C);
    }

    function last_id()
    {
        global $g;

        return $g->result("SELECT LAST_INSERT_ID()");
    }

    function explain($g, $G)
    {
        return $g->query("EXPLAIN " . (min_version(5.1) ? "PARTITIONS " : "") . $G);
    }

    function found_rows($S, $Z)
    {
        return ($Z || $S["Engine"] != "InnoDB" ? null : $S["Rows"]);
    }

    function types()
    {
        return
            [];
    }

    function schemas()
    {
        return
            [];
    }

    function get_schema()
    {
        return "";
    }

    function set_schema($Xg)
    {
        return
            true;
    }

    function create_sql($R, $Ma, $Eh)
    {
        global $g;
        $I = $g->result("SHOW CREATE TABLE " . table($R), 1);
        if (!$Ma) {
            $I = preg_replace('~ AUTO_INCREMENT=\d+~', '', $I);
        }

        return $I;
    }

    function truncate_sql($R)
    {
        return "TRUNCATE " . table($R);
    }

    function use_sql($j)
    {
        return "USE " . idf_escape($j);
    }

    function trigger_sql($R)
    {
        $I = "";
        foreach (get_rows("SHOW TRIGGERS LIKE " . q(addcslashes($R, "%_\\")), null, "-- ") as $J) {
            $I .= "\nCREATE TRIGGER " . idf_escape($J["Trigger"]) . " $J[Timing] $J[Event] ON " . table($J["Table"]) . " FOR EACH ROW\n$J[Statement];;\n";
        }

        return $I;
    }

    function show_variables()
    {
        return
            get_key_vals("SHOW VARIABLES");
    }

    function process_list()
    {
        return
            get_rows("SHOW FULL PROCESSLIST");
    }

    function show_status()
    {
        return
            get_key_vals("SHOW STATUS");
    }

    function convert_field($o)
    {
        if (preg_match("~binary~", $o["type"])) {
            return "HEX(" . idf_escape($o["field"]) . ")";
        }
        if ($o["type"] == "bit") {
            return "BIN(" . idf_escape($o["field"]) . " + 0)";
        }
        if (preg_match("~geometry|point|linestring|polygon~", $o["type"])) {
            return (min_version(8) ? "ST_" : "") . "AsWKT(" . idf_escape($o["field"]) . ")";
        }
    }

    function unconvert_field($o, $I)
    {
        if (preg_match("~binary~", $o["type"])) {
            $I = "UNHEX($I)";
        }
        if ($o["type"] == "bit") {
            $I = "CONV($I, 2, 10) + 0";
        }
        if (preg_match("~geometry|point|linestring|polygon~", $o["type"])) {
            $I = (min_version(8) ? "ST_" : "") . "GeomFromText($I)";
        }

        return $I;
    }

    function support($Qc)
    {
        return !preg_match("~scheme|sequence|type|view_trigger|materializedview" . (min_version(5.1) ? "" : "|event|partitioning" . (min_version(5) ? "" : "|routine|trigger|view")) . "~", $Qc);
    }

    function kill_process($X)
    {
        return
            queries("KILL " . number($X));
    }

    function connection_id()
    {
        return "SELECT CONNECTION_ID()";
    }

    function max_connections()
    {
        global $g;

        return $g->result("SELECT @@max_connections");
    }

    $x = "sql";
    $zi = [];
    $Dh = [];
    foreach ([lang(27) => ["tinyint" => 3, "smallint" => 5, "mediumint" => 8, "int" => 10, "bigint" => 20, "decimal" => 66, "float" => 12, "double" => 21], lang(28) => ["date" => 10, "datetime" => 19, "timestamp" => 19, "time" => 10, "year" => 4], lang(25) => ["char" => 255, "varchar" => 65535, "tinytext" => 255, "text" => 65535, "mediumtext" => 16777215, "longtext" => 4294967295], lang(33) => ["enum" => 65535, "set" => 64], lang(29) => ["bit" => 20, "binary" => 255, "varbinary" => 65535, "tinyblob" => 255, "blob" => 65535, "mediumblob" => 16777215, "longblob" => 4294967295], lang(31) => ["geometry" => 0, "point" => 0, "linestring" => 0, "polygon" => 0, "multipoint" => 0, "multilinestring" => 0, "multipolygon" => 0, "geometrycollection" => 0],] as $y => $X) {
        $zi += $X;
        $Dh[$y] = array_keys($X);
    }
    $Fi = ["unsigned", "zerofill", "unsigned zerofill"];
    $sf = ["=", "<", ">", "<=", ">=", "!=", "LIKE", "LIKE %%", "REGEXP", "IN", "FIND_IN_SET", "IS NULL", "NOT LIKE", "NOT REGEXP", "NOT IN", "IS NOT NULL", "SQL"];
    $ld = ["char_length", "date", "from_unixtime", "lower", "round", "floor", "ceil", "sec_to_time", "time_to_sec", "upper"];
    $rd = ["avg", "count", "count distinct", "group_concat", "max", "min", "sum"];
    $nc = [["char" => "md5/sha1/password/encrypt/uuid", "binary" => "md5/sha1", "date|time" => "now",], [number_type() => "+/-", "date" => "+ interval/- interval", "time" => "addtime/subtime", "char|text" => "concat",]];
}
define("SERVER", $_GET[DRIVER]);
define("DB", $_GET["db"]);
define("ME", preg_replace('~^[^?]*/([^?]*).*~', '\1', $_SERVER["REQUEST_URI"]) . '?' . (sid() ? SID . '&' : '') . (SERVER !== null ? DRIVER . "=" . urlencode(SERVER) . '&' : '') . (isset($_GET["username"]) ? "username=" . urlencode($_GET["username"]) . '&' : '') . (DB != "" ? 'db=' . urlencode(DB) . '&' . (isset($_GET["ns"]) ? "ns=" . urlencode($_GET["ns"]) . "&" : "") : ''));
$ia = "4.6.3";

class Adminer
{
    public $operators;

    public function name()
    {
        return "<a href='https://www.adminer.org/'" . target_blank() . " id='h1'>Adminer</a>";
    }

    public function credentials()
    {
        return
            [SERVER, $_GET["username"], get_password()];
    }

    public function connectSsl()
    {
    }

    public function permanentLogin($i = false)
    {
        return
            password_file($i);
    }

    public function bruteForceKey()
    {
        return $_SERVER["REMOTE_ADDR"];
    }

    public function serverName($N)
    {
        return
            h($N);
    }

    public function database()
    {
        return
            DB;
    }

    public function databases($bd = true)
    {
        return
            get_databases($bd);
    }

    public function schemas()
    {
        return
            schemas();
    }

    public function queryTimeout()
    {
        return
            2;
    }

    public function headers()
    {
    }

    public function csp()
    {
        return
            csp();
    }

    public function head()
    {
        return
            true;
    }

    public function css()
    {
        $I = [];
        $Vc = "adminer.css";
        if (file_exists($Vc)) {
            $I[] = $Vc;
        }

        return $I;
    }

    public function loginForm()
    {
        global $fc;
        echo "<table cellspacing='0'>\n", $this->loginFormField('driver', '<tr><th>' . lang(34) . '<td>', html_select("auth[driver]", $fc, DRIVER) . "\n"), $this->loginFormField('server', '<tr><th>' . lang(35) . '<td>', '<input name="auth[server]" value="' . h(SERVER) . '" title="hostname[:port]" placeholder="localhost" autocapitalize="off">' . "\n"), $this->loginFormField('username', '<tr><th>' . lang(36) . '<td>', '<input name="auth[username]" id="username" value="' . h($_GET["username"]) . '" autocapitalize="off">' . script("focus(qs('#username'));")), $this->loginFormField('password', '<tr><th>' . lang(37) . '<td>', '<input type="password" name="auth[password]">' . "\n"), $this->loginFormField('db', '<tr><th>' . lang(38) . '<td>', '<input name="auth[db]" value="' . h($_GET["db"]) . '" autocapitalize="off">' . "\n"), "</table>\n", "<p><input type='submit' value='" . lang(39) . "'>\n", checkbox("auth[permanent]", 1, $_COOKIE["adminer_permanent"], lang(40)) . "\n";
    }

    public function loginFormField($C, $yd, $Y)
    {
        return $yd . $Y;
    }

    public function login($xe, $F)
    {
        if ($F == "") {
            return
            lang(41, target_blank());
        }

        return
            true;
    }

    public function tableName($Kh)
    {
        return
            h($Kh["Name"]);
    }

    public function fieldName($o, $xf = 0)
    {
        return '<span title="' . h($o["full_type"]) . '">' . h($o["field"]) . '</span>';
    }

    public function selectLinks($Kh, $O = "")
    {
        global $x, $m;
        echo '<p class="links">';
        $we = ["select" => lang(42)];
        if (support("table") || support("indexes")) {
            $we["table"] = lang(43);
        }
        if (support("table")) {
            if (is_view($Kh)) {
                $we["view"] = lang(44);
            } else {
                $we["create"] = lang(45);
            }
        }
        if ($O !== null) {
            $we["edit"] = lang(46);
        }
        $C = $Kh["Name"];
        foreach ($we
                 as $y => $X) {
            echo " <a href='" . h(ME) . "$y=" . urlencode($C) . ($y == "edit" ? $O : "") . "'" . bold(isset($_GET[$y])) . ">$X</a>";
        }
        echo
        doc_link([$x => $m->tableHelp($C)], "?"), "\n";
    }

    public function foreignKeys($R)
    {
        return
            foreign_keys($R);
    }

    public function backwardKeys($R, $Jh)
    {
        return
            [];
    }

    public function backwardKeysPrint($Pa, $J)
    {
    }

    public function selectQuery($G, $_h, $Oc = false)
    {
        global $x, $m;
        $I = "</p>\n";
        if (!$Oc && ($Zi = $m->warnings())) {
            $t = "warnings";
            $I = ", <a href='#$t'>" . lang(47) . "</a>" . script("qsl('a').onclick = partial(toggle, '$t');", "") . "$I<div id='$t' class='hidden'>\n$Zi</div>\n";
        }

        return "<p><code class='jush-$x'>" . h(str_replace("\n", " ", $G)) . "</code> <span class='time'>(" . format_time($_h) . ")</span>" . (support("sql") ? " <a href='" . h(ME) . "sql=" . urlencode($G) . "'>" . lang(10) . "</a>" : "") . $I;
    }

    public function sqlCommandQuery($G)
    {
        return
            shorten_utf8(trim($G), 1000);
    }

    public function rowDescription($R)
    {
        return "";
    }

    public function rowDescriptions($K, $ed)
    {
        return $K;
    }

    public function selectLink($X, $o)
    {
    }

    public function selectVal($X, $_, $o, $Ef)
    {
        $I = ($X === null ? "<i>NULL</i>" : (preg_match("~char|binary|boolean~", $o["type"]) && !preg_match("~var~", $o["type"]) ? "<code>$X</code>" : $X));
        if (preg_match('~blob|bytea|raw|file~', $o["type"]) && !is_utf8($X)) {
            $I = "<i>" . lang(48, strlen($Ef)) . "</i>";
        }
        if (preg_match('~json~', $o["type"])) {
            $I = "<code class='jush-js'>$I</code>";
        }

        return ($_ ? "<a href='" . h($_) . "'" . (is_url($_) ? target_blank() : "") . ">$I</a>" : $I);
    }

    public function editVal($X, $o)
    {
        return $X;
    }

    public function tableStructurePrint($p)
    {
        echo "<table cellspacing='0' class='nowrap'>\n", "<thead><tr><th>" . lang(49) . "<td>" . lang(50) . (support("comment") ? "<td>" . lang(51) : "") . "</thead>\n";
        foreach ($p
                 as $o) {
            echo "<tr" . odd() . "><th>" . h($o["field"]), "<td><span title='" . h($o["collation"]) . "'>" . h($o["full_type"]) . "</span>", ($o["null"] ? " <i>NULL</i>" : ""), ($o["auto_increment"] ? " <i>" . lang(52) . "</i>" : ""), (isset($o["default"]) ? " <span title='" . lang(53) . "'>[<b>" . h($o["default"]) . "</b>]</span>" : ""), (support("comment") ? "<td>" . h($o["comment"]) : ""), "\n";
        }
        echo "</table>\n";
    }

    public function tableIndexesPrint($w)
    {
        echo "<table cellspacing='0'>\n";
        foreach ($w
                 as $C => $v) {
            ksort($v["columns"]);
            $jg = [];
            foreach ($v["columns"] as $y => $X) {
                $jg[] = "<i>" . h($X) . "</i>" . ($v["lengths"][$y] ? "(" . $v["lengths"][$y] . ")" : "") . ($v["descs"][$y] ? " DESC" : "");
            }
            echo "<tr title='" . h($C) . "'><th>$v[type]<td>" . implode(", ", $jg) . "\n";
        }
        echo "</table>\n";
    }

    public function selectColumnsPrint($L, $e)
    {
        global $ld, $rd;
        print_fieldset("select", lang(54), $L);
        $s = 0;
        $L[""] = [];
        foreach ($L
                 as $y => $X) {
            $X = $_GET["columns"][$y];
            $d = select_input(" name='columns[$s][col]'", $e, $X["col"], ($y !== "" ? "selectFieldChange" : "selectAddRow"));
            echo "<div>" . ($ld || $rd ? "<select name='columns[$s][fun]'>" . optionlist([-1 => ""] + array_filter([lang(55) => $ld, lang(56) => $rd]), $X["fun"]) . "</select>" . on_help("getTarget(event).value && getTarget(event).value.replace(/ |\$/, '(') + ')'", 1) . script("qsl('select').onchange = function () { helpClose();" . ($y !== "" ? "" : " qsl('select, input', this.parentNode).onchange();") . " };", "") . "($d)" : $d) . "</div>\n";
            $s++;
        }
        echo "</div></fieldset>\n";
    }

    public function selectSearchPrint($Z, $e, $w)
    {
        print_fieldset("search", lang(57), $Z);
        foreach ($w
                 as $s => $v) {
            if ($v["type"] == "FULLTEXT") {
                echo "<div>(<i>" . implode("</i>, <i>", array_map('h', $v["columns"])) . "</i>) AGAINST", " <input type='search' name='fulltext[$s]' value='" . h($_GET["fulltext"][$s]) . "'>", script("qsl('input').oninput = selectFieldChange;", ""), checkbox("boolean[$s]", 1, isset($_GET["boolean"][$s]), "BOOL"), "</div>\n";
            }
        }
        $bb = "this.parentNode.firstChild.onchange();";
        foreach (array_merge((array)$_GET["where"], [[]]) as $s => $X) {
            if (!$X || ("$X[col]$X[val]" != "" && in_array($X["op"], $this->operators))) {
                echo "<div>" . select_input(" name='where[$s][col]'", $e, $X["col"], ($X ? "selectFieldChange" : "selectAddRow"), "(" . lang(58) . ")"), html_select("where[$s][op]", $this->operators, $X["op"], $bb), "<input type='search' name='where[$s][val]' value='" . h($X["val"]) . "'>", script("mixin(qsl('input'), {oninput: function () { $bb }, onkeydown: selectSearchKeydown, onsearch: selectSearchSearch});", ""), "</div>\n";
            }
        }
        echo "</div></fieldset>\n";
    }

    public function selectOrderPrint($xf, $e, $w)
    {
        print_fieldset("sort", lang(59), $xf);
        $s = 0;
        foreach ((array)$_GET["order"] as $y => $X) {
            if ($X != "") {
                echo "<div>" . select_input(" name='order[$s]'", $e, $X, "selectFieldChange"), checkbox("desc[$s]", 1, isset($_GET["desc"][$y]), lang(60)) . "</div>\n";
                $s++;
            }
        }
        echo "<div>" . select_input(" name='order[$s]'", $e, "", "selectAddRow"), checkbox("desc[$s]", 1, false, lang(60)) . "</div>\n", "</div></fieldset>\n";
    }

    public function selectLimitPrint($z)
    {
        echo "<fieldset><legend>" . lang(61) . "</legend><div>";
        echo "<input type='number' name='limit' class='size' value='" . h($z) . "'>", script("qsl('input').oninput = selectFieldChange;", ""), "</div></fieldset>\n";
    }

    public function selectLengthPrint($Zh)
    {
        if ($Zh !== null) {
            echo "<fieldset><legend>" . lang(62) . "</legend><div>", "<input type='number' name='text_length' class='size' value='" . h($Zh) . "'>", "</div></fieldset>\n";
        }
    }

    public function selectActionPrint($w)
    {
        echo "<fieldset><legend>" . lang(63) . "</legend><div>", "<input type='submit' value='" . lang(54) . "'>", " <span id='noindex' title='" . lang(64) . "'></span>", "<script" . nonce() . ">\n", "var indexColumns = ";
        $e = [];
        foreach ($w
                 as $v) {
            $Lb = reset($v["columns"]);
            if ($v["type"] != "FULLTEXT" && $Lb) {
                $e[$Lb] = 1;
            }
        }
        $e[""] = 1;
        foreach ($e
                 as $y => $X) {
            json_row($y);
        }
        echo ";\n", "selectFieldChange.call(qs('#form')['select']);\n", "</script>\n", "</div></fieldset>\n";
    }

    public function selectCommandPrint()
    {
        return !information_schema(DB);
    }

    public function selectImportPrint()
    {
        return !information_schema(DB);
    }

    public function selectEmailPrint($sc, $e)
    {
    }

    public function selectColumnsProcess($e, $w)
    {
        global $ld, $rd;
        $L = [];
        $od = [];
        foreach ((array)$_GET["columns"] as $y => $X) {
            if ($X["fun"] == "count" || ($X["col"] != "" && (!$X["fun"] || in_array($X["fun"], $ld) || in_array($X["fun"], $rd)))) {
                $L[$y] = apply_sql_function($X["fun"], ($X["col"] != "" ? idf_escape($X["col"]) : "*"));
                if (!in_array($X["fun"], $rd)) {
                    $od[] = $L[$y];
                }
            }
        }

        return
            [$L, $od];
    }

    public function selectSearchProcess($p, $w)
    {
        global $g, $m;
        $I = [];
        foreach ($w
                 as $s => $v) {
            if ($v["type"] == "FULLTEXT" && $_GET["fulltext"][$s] != "") {
                $I[] = "MATCH (" . implode(", ", array_map('idf_escape', $v["columns"])) . ") AGAINST (" . q($_GET["fulltext"][$s]) . (isset($_GET["boolean"][$s]) ? " IN BOOLEAN MODE" : "") . ")";
            }
        }
        foreach ((array)$_GET["where"] as $y => $X) {
            if ("$X[col]$X[val]" != "" && in_array($X["op"], $this->operators)) {
                $fg = "";
                $xb = " $X[op]";
                if (preg_match('~IN$~', $X["op"])) {
                    $Gd = process_length($X["val"]);
                    $xb .= " " . ($Gd != "" ? $Gd : "(NULL)");
                } elseif ($X["op"] == "SQL") {
                    $xb = " $X[val]";
                } elseif ($X["op"] == "LIKE %%") {
                    $xb = " LIKE " . $this->processInput($p[$X["col"]], "%$X[val]%");
                } elseif ($X["op"] == "ILIKE %%") {
                    $xb = " ILIKE " . $this->processInput($p[$X["col"]], "%$X[val]%");
                } elseif ($X["op"] == "FIND_IN_SET") {
                    $fg = "$X[op](" . q($X["val"]) . ", ";
                    $xb = ")";
                } elseif (!preg_match('~NULL$~', $X["op"])) {
                    $xb .= " " . $this->processInput($p[$X["col"]], $X["val"]);
                }
                if ($X["col"] != "") {
                    $I[] = $fg . $m->convertSearch(idf_escape($X["col"]), $X, $p[$X["col"]]) . $xb;
                } else {
                    $sb = [];
                    foreach ($p
                             as $C => $o) {
                        if ((preg_match('~^[-\d.' . (preg_match('~IN$~', $X["op"]) ? ',' : '') . ']+$~', $X["val"]) || !preg_match('~' . number_type() . '|bit~', $o["type"])) && (!preg_match("~[\x80-\xFF]~", $X["val"]) || preg_match('~char|text|enum|set~', $o["type"]))) {
                            $sb[] = $fg . $m->convertSearch(idf_escape($C), $X, $o) . $xb;
                        }
                    }
                    $I[] = ($sb ? "(" . implode(" OR ", $sb) . ")" : "1 = 0");
                }
            }
        }

        return $I;
    }

    public function selectOrderProcess($p, $w)
    {
        $I = [];
        foreach ((array)$_GET["order"] as $y => $X) {
            if ($X != "") {
                $I[] = (preg_match('~^((COUNT\(DISTINCT |[A-Z0-9_]+\()(`(?:[^`]|``)+`|"(?:[^"]|"")+")\)|COUNT\(\*\))$~', $X) ? $X : idf_escape($X)) . (isset($_GET["desc"][$y]) ? " DESC" : "");
            }
        }

        return $I;
    }

    public function selectLimitProcess()
    {
        return (isset($_GET["limit"]) ? $_GET["limit"] : "50");
    }

    public function selectLengthProcess()
    {
        return (isset($_GET["text_length"]) ? $_GET["text_length"] : "100");
    }

    public function selectEmailProcess($Z, $ed)
    {
        return
            false;
    }

    public function selectQueryBuild($L, $Z, $od, $xf, $z, $E)
    {
        return "";
    }

    public function messageQuery($G, $ai, $Oc = false)
    {
        global $x, $m;
        restart_session();
        $zd =& get_session("queries");
        if (!$zd[$_GET["db"]]) {
            $zd[$_GET["db"]] = [];
        }
        if (strlen($G) > 1e6) {
            $G = preg_replace('~[\x80-\xFF]+$~', '', substr($G, 0, 1e6)) . "\n...";
        }
        $zd[$_GET["db"]][] = [$G, time(), $ai];
        $xh = "sql-" . count($zd[$_GET["db"]]);
        $I = "<a href='#$xh' class='toggle'>" . lang(65) . "</a>\n";
        if (!$Oc && ($Zi = $m->warnings())) {
            $t = "warnings-" . count($zd[$_GET["db"]]);
            $I = "<a href='#$t' class='toggle'>" . lang(47) . "</a>, $I<div id='$t' class='hidden'>\n$Zi</div>\n";
        }

        return " <span class='time'>" . @date("H:i:s") . "</span>" . " $I<div id='$xh' class='hidden'><pre><code class='jush-$x'>" . shorten_utf8($G, 1000) . "</code></pre>" . ($ai ? " <span class='time'>($ai)</span>" : '') . (support("sql") ? '<p><a href="' . h(str_replace("db=" . urlencode(DB), "db=" . urlencode($_GET["db"]), ME) . 'sql=&history=' . (count($zd[$_GET["db"]]) - 1)) . '">' . lang(10) . '</a>' : '') . '</div>';
    }

    public function editFunctions($o)
    {
        global $nc;
        $I = ($o["null"] ? "NULL/" : "");
        foreach ($nc
                 as $y => $ld) {
            if (!$y || (!isset($_GET["call"]) && (isset($_GET["select"]) || where($_GET)))) {
                foreach ($ld
                         as $Xf => $X) {
                    if (!$Xf || preg_match("~$Xf~", $o["type"])) {
                        $I .= "/$X";
                    }
                }
                if ($y && !preg_match('~set|blob|bytea|raw|file~', $o["type"])) {
                    $I .= "/SQL";
                }
            }
        }
        if ($o["auto_increment"] && !isset($_GET["select"]) && !where($_GET)) {
            $I = lang(52);
        }

        return
            explode("/", $I);
    }

    public function editInput($R, $o, $Ka, $Y)
    {
        if ($o["type"] == "enum") {
            return (isset($_GET["select"]) ? "<label><input type='radio'$Ka value='-1' checked><i>" . lang(8) . "</i></label> " : "") . ($o["null"] ? "<label><input type='radio'$Ka value=''" . ($Y !== null || isset($_GET["select"]) ? "" : " checked") . "><i>NULL</i></label> " : "") . enum_input("radio", $Ka, $o, $Y, 0);
        }

        return "";
    }

    public function editHint($R, $o, $Y)
    {
        return "";
    }

    public function processInput($o, $Y, $r = "")
    {
        if ($r == "SQL") {
            return $Y;
        }
        $C = $o["field"];
        $I = q($Y);
        if (preg_match('~^(now|getdate|uuid)$~', $r)) {
            $I = "$r()";
        } elseif (preg_match('~^current_(date|timestamp)$~', $r)) {
            $I = $r;
        } elseif (preg_match('~^([+-]|\|\|)$~', $r)) {
            $I = idf_escape($C) . " $r $I";
        } elseif (preg_match('~^[+-] interval$~', $r)) {
            $I = idf_escape($C) . " $r " . (preg_match("~^(\\d+|'[0-9.: -]') [A-Z_]+\$~i", $Y) ? $Y : $I);
        } elseif (preg_match('~^(addtime|subtime|concat)$~', $r)) {
            $I = "$r(" . idf_escape($C) . ", $I)";
        } elseif (preg_match('~^(md5|sha1|password|encrypt)$~', $r)) {
            $I = "$r($I)";
        }

        return
            unconvert_field($o, $I);
    }

    public function dumpOutput()
    {
        $I = ['text' => lang(66), 'file' => lang(67)];
        if (function_exists('gzencode')) {
            $I['gz'] = 'gzip';
        }

        return $I;
    }

    public function dumpFormat()
    {
        return
            ['sql' => 'SQL', 'csv' => 'CSV,', 'csv;' => 'CSV;', 'tsv' => 'TSV'];
    }

    public function dumpDatabase($l)
    {
    }

    public function dumpTable($R, $Eh, $Zd = 0)
    {
        if ($_POST["format"] != "sql") {
            echo "\xef\xbb\xbf";
            if ($Eh) {
                dump_csv(array_keys(fields($R)));
            }
        } else {
            if ($Zd == 2) {
                $p = [];
                foreach (fields($R) as $C => $o) {
                    $p[] = idf_escape($C) . " $o[full_type]";
                }
                $i = "CREATE TABLE " . table($R) . " (" . implode(", ", $p) . ")";
            } else {
                $i = create_sql($R, $_POST["auto_increment"], $Eh);
            }
            set_utf8mb4($i);
            if ($Eh && $i) {
                if ($Eh == "DROP+CREATE" || $Zd == 1) {
                    echo "DROP " . ($Zd == 2 ? "VIEW" : "TABLE") . " IF EXISTS " . table($R) . ";\n";
                }
                if ($Zd == 1) {
                    $i = remove_definer($i);
                }
                echo "$i;\n\n";
            }
        }
    }

    public function dumpData($R, $Eh, $G)
    {
        global $g, $x;
        $Ee = ($x == "sqlite" ? 0 : 1048576);
        if ($Eh) {
            if ($_POST["format"] == "sql") {
                if ($Eh == "TRUNCATE+INSERT") {
                    echo
                    truncate_sql($R) . ";\n";
                }
                $p = fields($R);
            }
            $H = $g->query($G, 1);
            if ($H) {
                $Sd = "";
                $Ya = "";
                $ge = [];
                $Gh = "";
                $Rc = ($R != '' ? 'fetch_assoc' : 'fetch_row');
                while ($J = $H->$Rc()) {
                    if (!$ge) {
                        $Ri = [];
                        foreach ($J
                                 as $X) {
                            $o = $H->fetch_field();
                            $ge[] = $o->name;
                            $y = idf_escape($o->name);
                            $Ri[] = "$y = VALUES($y)";
                        }
                        $Gh = ($Eh == "INSERT+UPDATE" ? "\nON DUPLICATE KEY UPDATE " . implode(", ", $Ri) : "") . ";\n";
                    }
                    if ($_POST["format"] != "sql") {
                        if ($Eh == "table") {
                            dump_csv($ge);
                            $Eh = "INSERT";
                        }
                        dump_csv($J);
                    } else {
                        if (!$Sd) {
                            $Sd = "INSERT INTO " . table($R) . " (" . implode(", ", array_map('idf_escape', $ge)) . ") VALUES";
                        }
                        foreach ($J
                                 as $y => $X) {
                            $o = $p[$y];
                            $J[$y] = ($X !== null ? unconvert_field($o, preg_match(number_type(), $o["type"]) && $X != '' ? $X : q(($X === false ? 0 : $X))) : "NULL");
                        }
                        $Vg = ($Ee ? "\n" : " ") . "(" . implode(",\t", $J) . ")";
                        if (!$Ya) {
                            $Ya = $Sd . $Vg;
                        } elseif (strlen($Ya) + 4 + strlen($Vg) + strlen($Gh) < $Ee) {
                            $Ya .= ",$Vg";
                        } else {
                            echo $Ya . $Gh;
                            $Ya = $Sd . $Vg;
                        }
                    }
                }
                if ($Ya) {
                    echo $Ya . $Gh;
                }
            } elseif ($_POST["format"] == "sql") {
                echo "-- " . str_replace("\n", " ", $g->error) . "\n";
            }
        }
    }

    public function dumpFilename($Dd)
    {
        return
            friendly_url($Dd != "" ? $Dd : (SERVER != "" ? SERVER : "localhost"));
    }

    public function dumpHeaders($Dd, $Te = false)
    {
        $Hf = $_POST["output"];
        $Jc = (preg_match('~sql~', $_POST["format"]) ? "sql" : ($Te ? "tar" : "csv"));
        header("Content-Type: " . ($Hf == "gz" ? "application/x-gzip" : ($Jc == "tar" ? "application/x-tar" : ($Jc == "sql" || $Hf != "file" ? "text/plain" : "text/csv") . "; charset=utf-8")));
        if ($Hf == "gz") {
            ob_start('ob_gzencode', 1e6);
        }

        return $Jc;
    }

    public function importServerPath()
    {
        return "adminer.sql";
    }

    public function homepage()
    {
        echo '<p class="links">' . ($_GET["ns"] == "" && support("database") ? '<a href="' . h(ME) . 'database=">' . lang(68) . "</a>\n" : ""), (support("scheme") ? "<a href='" . h(ME) . "scheme='>" . ($_GET["ns"] != "" ? lang(69) : lang(70)) . "</a>\n" : ""), ($_GET["ns"] !== "" ? '<a href="' . h(ME) . 'schema=">' . lang(71) . "</a>\n" : ""), (support("privileges") ? "<a href='" . h(ME) . "privileges='>" . lang(72) . "</a>\n" : "");

        return
            true;
    }

    public function navigation($Se)
    {
        global $ia, $x, $fc, $g;
        echo '<h1>
', $this->name(), ' <span class="version">', $ia, '</span>
<a href="https://www.adminer.org/#download"', target_blank(), ' id="version">', (version_compare($ia, $_COOKIE["adminer_version"]) < 0 ? h($_COOKIE["adminer_version"]) : ""), '</a>
</h1>
';
        if ($Se == "auth") {
            $Xc = true;
            foreach ((array)$_SESSION["pwds"] as $Ti => $jh) {
                foreach ($jh
                         as $N => $Oi) {
                    foreach ($Oi
                             as $V => $F) {
                        if ($F !== null) {
                            if ($Xc) {
                                echo "<p id='logins'>" . script("mixin(qs('#logins'), {onmouseover: menuOver, onmouseout: menuOut});");
                                $Xc = false;
                            }
                            $Rb = $_SESSION["db"][$Ti][$N][$V];
                            foreach (($Rb ? array_keys($Rb) : [""]) as $l) {
                                echo "<a href='" . h(auth_url($Ti, $N, $V, $l)) . "'>($fc[$Ti]) " . h($V . ($N != "" ? "@" . $this->serverName($N) : "") . ($l != "" ? " - $l" : "")) . "</a><br>\n";
                            }
                        }
                    }
                }
            }
        } else {
            if ($_GET["ns"] !== "" && !$Se && DB != "") {
                $g->select_db(DB);
                $T = table_status('', true);
            }
            echo
            script_src(preg_replace("~\\?.*~", "", ME) . "?file=jush.js&version=4.6.3");
            if (support("sql")) {
                echo '<script', nonce(), '>
';
                if ($T) {
                    $we = [];
                    foreach ($T
                             as $R => $U) {
                        $we[] = preg_quote($R, '/');
                    }
                    echo "var jushLinks = { $x: [ '" . js_escape(ME) . (support("table") ? "table=" : "select=") . "\$&', /\\b(" . implode("|", $we) . ")\\b/g ] };\n";
                    foreach (["bac", "bra", "sqlite_quo", "mssql_bra"] as $X) {
                        echo "jushLinks.$X = jushLinks.$x;\n";
                    }
                }
                $ih = $g->server_info;
                echo 'bodyLoad(\'', (is_object($g) ? preg_replace('~^(\d\.?\d).*~s', '\1', $ih) : ""), '\'', (preg_match('~MariaDB~', $ih) ? ", true" : ""), ');
</script>
';
            }
            $this->databasesPrint($Se);
            if (DB == "" || !$Se) {
                echo "<p class='links'>" . (support("sql") ? "<a href='" . h(ME) . "sql='" . bold(isset($_GET["sql"]) && !isset($_GET["import"])) . ">" . lang(65) . "</a>\n<a href='" . h(ME) . "import='" . bold(isset($_GET["import"])) . ">" . lang(73) . "</a>\n" : "") . "";
                if (support("dump")) {
                    echo "<a href='" . h(ME) . "dump=" . urlencode(isset($_GET["table"]) ? $_GET["table"] : $_GET["select"]) . "' id='dump'" . bold(isset($_GET["dump"])) . ">" . lang(74) . "</a>\n";
                }
            }
            if ($_GET["ns"] !== "" && !$Se && DB != "") {
                echo '<a href="' . h(ME) . 'create="' . bold($_GET["create"] === "") . ">" . lang(75) . "</a>\n";
                if (!$T) {
                    echo "<p class='message'>" . lang(9) . "\n";
                } else {
                    $this->tablesPrint($T);
                }
            }
        }
    }

    public function databasesPrint($Se)
    {
        global $b, $g;
        $k = $this->databases();
        if ($k && !in_array(DB, $k)) {
            array_unshift($k, DB);
        }
        echo '<form action="">
<p id="dbs">
';
        hidden_fields_get();
        $Pb = script("mixin(qsl('select'), {onmousedown: dbMouseDown, onchange: dbChange});");
        echo "<span title='" . lang(76) . "'>" . lang(77) . "</span>: " . ($k ? "<select name='db'>" . optionlist(["" => ""] + $k, DB) . "</select>$Pb" : "<input name='db' value='" . h(DB) . "' autocapitalize='off'>\n"), "<input type='submit' value='" . lang(20) . "'" . ($k ? " class='hidden'" : "") . ">\n";
        if ($Se != "db" && DB != "" && $g->select_db(DB)) {
            if (support("scheme")) {
                echo "<br>" . lang(78) . ": <select name='ns'>" . optionlist(["" => ""] + $b->schemas(), $_GET["ns"]) . "</select>$Pb";
                if ($_GET["ns"] != "") {
                    set_schema($_GET["ns"]);
                }
            }
        }
        foreach (["import", "sql", "schema", "dump", "privileges"] as $X) {
            if (isset($_GET[$X])) {
                echo "<input type='hidden' name='$X' value=''>";
                break;
            }
        }
        echo "</p></form>\n";
    }

    public function tablesPrint($T)
    {
        echo "<ul id='tables'>" . script("mixin(qs('#tables'), {onmouseover: menuOver, onmouseout: menuOut});");
        foreach ($T
                 as $R => $P) {
            $C = $this->tableName($P);
            if ($C != "") {
                echo '<li><a href="' . h(ME) . 'select=' . urlencode($R) . '"' . bold($_GET["select"] == $R || $_GET["edit"] == $R, "select") . ">" . lang(79) . "</a> ", (support("table") || support("indexes") ? '<a href="' . h(ME) . 'table=' . urlencode($R) . '"' . bold(in_array($R, [$_GET["table"], $_GET["create"], $_GET["indexes"], $_GET["foreign"], $_GET["trigger"]]), (is_view($P) ? "view" : "structure")) . " title='" . lang(43) . "'>$C</a>" : "<span>$C</span>") . "\n";
            }
        }
        echo "</ul>\n";
    }
}

$b = (function_exists('adminer_object') ? adminer_object() : new
Adminer);
if ($b->operators === null) {
    $b->operators = $sf;
}
function page_header($di, $n = "", $Xa = [], $ei = "")
{
    global $ca, $ia, $b, $fc, $x;
    page_headers();
    if (is_ajax() && $n) {
        page_messages($n);
        exit;
    }
    $fi = $di . ($ei != "" ? ": $ei" : "");
    $gi = strip_tags($fi . (SERVER != "" && SERVER != "localhost" ? h(" - " . SERVER) : "") . " - " . $b->name());
    echo '<!DOCTYPE html>
<html lang="', $ca, '" dir="', lang(80), '">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex">
<title>', $gi, '</title>
<link rel="stylesheet" type="text/css" href="', h(preg_replace("~\\?.*~", "", ME) . "?file=default.css&version=4.6.3"), '">
', script_src(preg_replace("~\\?.*~", "", ME) . "?file=functions.js&version=4.6.3");
    if ($b->head()) {
        echo '<link rel="shortcut icon" type="image/x-icon" href="', h(preg_replace("~\\?.*~", "", ME) . "?file=favicon.ico&version=4.6.3"), '">
<link rel="apple-touch-icon" href="', h(preg_replace("~\\?.*~", "", ME) . "?file=favicon.ico&version=4.6.3"), '">
';
        foreach ($b->css() as $Jb) {
            echo '<link rel="stylesheet" type="text/css" href="', h($Jb), '">
';
        }
    }
    echo '
<body class="', lang(80), ' nojs">
';
    $Vc = get_temp_dir() . "/adminer.version";
    if (!$_COOKIE["adminer_version"] && function_exists('openssl_verify') && file_exists($Vc) && filemtime($Vc) + 86400 > time()) {
        $Ui = unserialize(file_get_contents($Vc));
        $qg = "-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwqWOVuF5uw7/+Z70djoK
RlHIZFZPO0uYRezq90+7Amk+FDNd7KkL5eDve+vHRJBLAszF/7XKXe11xwliIsFs
DFWQlsABVZB3oisKCBEuI71J4kPH8dKGEWR9jDHFw3cWmoH3PmqImX6FISWbG3B8
h7FIx3jEaw5ckVPVTeo5JRm/1DZzJxjyDenXvBQ/6o9DgZKeNDgxwKzH+sw9/YCO
jHnq1cFpOIISzARlrHMa/43YfeNRAm/tsBXjSxembBPo7aQZLAWHmaj5+K19H10B
nCpz9Y++cipkVEiKRGih4ZEvjoFysEOdRLj6WiD/uUNky4xGeA6LaJqh5XpkFkcQ
fQIDAQAB
-----END PUBLIC KEY-----
";
        if (openssl_verify($Ui["version"], base64_decode($Ui["signature"]), $qg) == 1) {
            $_COOKIE["adminer_version"] = $Ui["version"];
        }
    }
    echo '<script', nonce(), '>
mixin(document.body, {onkeydown: bodyKeydown, onclick: bodyClick', (isset($_COOKIE["adminer_version"]) ? "" : ", onload: partial(verifyVersion, '$ia', '" . js_escape(ME) . "', '" . get_token() . "')"); ?>});
    document.body.className = document.body.className.replace(/ nojs/, ' js');
    var offlineMessage = '<?php echo
js_escape(lang(81)), '\';
var thousandsSeparator = \'', js_escape(lang(5)), '\';
</script>

<div id="help" class="jush-', $x, ' jsonly hidden"></div>
', script("mixin(qs('#help'), {onmouseover: function () { helpOpen = 1; }, onmouseout: helpMouseout});"), '
<div id="content">
';
    if ($Xa !== null) {
        $_ = substr(preg_replace('~\b(username|db|ns)=[^&]*&~', '', ME), 0, -1);
        echo '<p id="breadcrumb"><a href="' . h($_ ? $_ : ".") . '">' . $fc[DRIVER] . '</a> &raquo; ';
        $_ = substr(preg_replace('~\b(db|ns)=[^&]*&~', '', ME), 0, -1);
        $N = $b->serverName(SERVER);
        $N = ($N != "" ? $N : lang(35));
        if ($Xa === false) {
            echo "$N\n";
        } else {
            echo "<a href='" . ($_ ? h($_) : ".") . "' accesskey='1' title='Alt+Shift+1'>$N</a> &raquo; ";
            if ($_GET["ns"] != "" || (DB != "" && is_array($Xa))) {
                echo '<a href="' . h($_ . "&db=" . urlencode(DB) . (support("scheme") ? "&ns=" : "")) . '">' . h(DB) . '</a> &raquo; ';
            }
            if (is_array($Xa)) {
                if ($_GET["ns"] != "") {
                    echo '<a href="' . h(substr(ME, 0, -1)) . '">' . h($_GET["ns"]) . '</a> &raquo; ';
                }
                foreach ($Xa
                         as $y => $X) {
                    $Xb = (is_array($X) ? $X[1] : h($X));
                    if ($Xb != "") {
                        echo "<a href='" . h(ME . "$y=") . urlencode(is_array($X) ? $X[0] : $X) . "'>$Xb</a> &raquo; ";
                    }
                }
            }
            echo "$di\n";
        }
    }
    echo "<h2>$fi</h2>\n", "<div id='ajaxstatus' class='jsonly hidden'></div>\n";
    restart_session();
    page_messages($n);
    $k =& get_session("dbs");
    if (DB != "" && $k && !in_array(DB, $k, true)) {
        $k = null;
    }
    stop_session();
    define("PAGE_HEADER", 1);
}

function page_headers()
{
    global $b;
    header("Content-Type: text/html; charset=utf-8");
    header("Cache-Control: no-cache");
    header("X-Frame-Options: deny");
    header("X-XSS-Protection: 0");
    header("X-Content-Type-Options: nosniff");
    header("Referrer-Policy: origin-when-cross-origin");
    foreach ($b->csp() as $Ib) {
        $xd = [];
        foreach ($Ib
                 as $y => $X) {
            $xd[] = "$y $X";
        }
        header("Content-Security-Policy: " . implode("; ", $xd));
    }
    $b->headers();
}

function csp()
{
    return
        [["script-src" => "'self' 'unsafe-inline' 'nonce-" . get_nonce() . "' 'strict-dynamic'", "connect-src" => "'self'", "frame-src" => "https://www.adminer.org", "object-src" => "'none'", "base-uri" => "'none'", "form-action" => "'self'",],];
}

function get_nonce()
{
    static $cf;
    if (!$cf) {
        $cf = base64_encode(rand_string());
    }

    return $cf;
}

function page_messages($n)
{
    $Hi = preg_replace('~^[^?]*~', '', $_SERVER["REQUEST_URI"]);
    $Oe = $_SESSION["messages"][$Hi];
    if ($Oe) {
        echo "<div class='message'>" . implode("</div>\n<div class='message'>", $Oe) . "</div>" . script("messagesPrint();");
        unset($_SESSION["messages"][$Hi]);
    }
    if ($n) {
        echo "<div class='error'>$n</div>\n";
    }
}

function page_footer($Se = "")
{
    global $b, $ki;
    echo '</div>

';
    switch_lang();
    if ($Se != "auth") {
        echo '<form action="" method="post">
<p class="logout">
<input type="submit" name="logout" value="', lang(82), '" id="logout">
<input type="hidden" name="token" value="', $ki, '">
</p>
</form>
';
    }
    echo '<div id="menu">
';
    $b->navigation($Se);
    echo '</div>
', script("setupSubmitHighlight(document);");
}

function int32($Ve)
{
    while ($Ve >= 2147483648) {
        $Ve -= 4294967296;
    }
    while ($Ve <= -2147483649) {
        $Ve += 4294967296;
    }

    return (int)$Ve;
}

function long2str($W, $Yi)
{
    $Vg = '';
    foreach ($W
             as $X) {
        $Vg .= pack('V', $X);
    }
    if ($Yi) {
        return
        substr($Vg, 0, end($W));
    }

    return $Vg;
}

function str2long($Vg, $Yi)
{
    $W = array_values(unpack('V*', str_pad($Vg, 4 * ceil(strlen($Vg) / 4), "\0")));
    if ($Yi) {
        $W[] = strlen($Vg);
    }

    return $W;
}

function xxtea_mx($lj, $kj, $Hh, $ce)
{
    return
        int32((($lj >> 5 & 0x7FFFFFF) ^ $kj << 2) + (($kj >> 3 & 0x1FFFFFFF) ^ $lj << 4)) ^ int32(($Hh ^ $kj) + ($ce ^ $lj));
}

function encrypt_string($Ch, $y)
{
    if ($Ch == "") {
        return "";
    }
    $y = array_values(unpack("V*", pack("H*", md5($y))));
    $W = str2long($Ch, true);
    $Ve = count($W) - 1;
    $lj = $W[$Ve];
    $kj = $W[0];
    $rg = floor(6 + 52 / ($Ve + 1));
    $Hh = 0;
    while ($rg-- > 0) {
        $Hh = int32($Hh + 0x9E3779B9);
        $mc = $Hh >> 2 & 3;
        for ($If = 0; $If < $Ve; $If++) {
            $kj = $W[$If + 1];
            $Ue = xxtea_mx($lj, $kj, $Hh, $y[$If & 3 ^ $mc]);
            $lj = int32($W[$If] + $Ue);
            $W[$If] = $lj;
        }
        $kj = $W[0];
        $Ue = xxtea_mx($lj, $kj, $Hh, $y[$If & 3 ^ $mc]);
        $lj = int32($W[$Ve] + $Ue);
        $W[$Ve] = $lj;
    }

    return
        long2str($W, false);
}

function decrypt_string($Ch, $y)
{
    if ($Ch == "") {
        return "";
    }
    if (!$y) {
        return
        false;
    }
    $y = array_values(unpack("V*", pack("H*", md5($y))));
    $W = str2long($Ch, false);
    $Ve = count($W) - 1;
    $lj = $W[$Ve];
    $kj = $W[0];
    $rg = floor(6 + 52 / ($Ve + 1));
    $Hh = int32($rg * 0x9E3779B9);
    while ($Hh) {
        $mc = $Hh >> 2 & 3;
        for ($If = $Ve; $If > 0; $If--) {
            $lj = $W[$If - 1];
            $Ue = xxtea_mx($lj, $kj, $Hh, $y[$If & 3 ^ $mc]);
            $kj = int32($W[$If] - $Ue);
            $W[$If] = $kj;
        }
        $lj = $W[$Ve];
        $Ue = xxtea_mx($lj, $kj, $Hh, $y[$If & 3 ^ $mc]);
        $kj = int32($W[0] - $Ue);
        $W[0] = $kj;
        $Hh = int32($Hh - 0x9E3779B9);
    }

    return
        long2str($W, true);
}

$g = '';
$wd = $_SESSION["token"];
if (!$wd) {
    $_SESSION["token"] = rand(1, 1e6);
}
$ki = get_token();
$Yf = [];
if ($_COOKIE["adminer_permanent"]) {
    foreach (explode(" ", $_COOKIE["adminer_permanent"]) as $X) {
        list($y) = explode(":", $X);
        $Yf[$y] = $X;
    }
}
function add_invalid_login()
{
    global $b;
    $jd = file_open_lock(get_temp_dir() . "/adminer.invalid");
    if (!$jd) {
        return;
    }
    $Vd = unserialize(stream_get_contents($jd));
    $ai = time();
    if ($Vd) {
        foreach ($Vd
                 as $Wd => $X) {
            if ($X[0] < $ai) {
                unset($Vd[$Wd]);
            }
        }
    }
    $Ud =& $Vd[$b->bruteForceKey()];
    if (!$Ud) {
        $Ud = [$ai + 30 * 60, 0];
    }
    $Ud[1]++;
    file_write_unlock($jd, serialize($Vd));
}

function check_invalid_login()
{
    global $b;
    $Vd = unserialize(@file_get_contents(get_temp_dir() . "/adminer.invalid"));
    $Ud = $Vd[$b->bruteForceKey()];
    $bf = ($Ud[1] > 29 ? $Ud[0] - time() : 0);
    if ($bf > 0) {
        auth_error(lang(83, ceil($bf / 60)));
    }
}

$La = $_POST["auth"];
if ($La) {
    session_regenerate_id();
    $Ti = $La["driver"];
    $N = $La["server"];
    $V = $La["username"];
    $F = (string)$La["password"];
    $l = $La["db"];
    set_password($Ti, $N, $V, $F);
    $_SESSION["db"][$Ti][$N][$V][$l] = true;
    if ($La["permanent"]) {
        $y = base64_encode($Ti) . "-" . base64_encode($N) . "-" . base64_encode($V) . "-" . base64_encode($l);
        $kg = $b->permanentLogin(true);
        $Yf[$y] = "$y:" . base64_encode($kg ? encrypt_string($F, $kg) : "");
        cookie("adminer_permanent", implode(" ", $Yf));
    }
    if (count($_POST) == 1 || DRIVER != $Ti || SERVER != $N || $_GET["username"] !== $V || DB != $l) {
        redirect(auth_url($Ti, $N, $V, $l));
    }
} elseif ($_POST["logout"]) {
    if ($wd && !verify_token()) {
        page_header(lang(82), lang(84));
        page_footer("db");
        exit;
    } else {
        foreach (["pwds", "db", "dbs", "queries"] as $y) {
            set_session($y, null);
        }
        unset_permanent();
        redirect(substr(preg_replace('~\b(username|db|ns)=[^&]*&~', '', ME), 0, -1), lang(85) . ' ' . lang(86, 'https://sourceforge.net/donate/index.php?group_id=264133'));
    }
} elseif ($Yf && !$_SESSION["pwds"]) {
    session_regenerate_id();
    $kg = $b->permanentLogin();
    foreach ($Yf
             as $y => $X) {
        list(, $jb) = explode(":", $X);
        list($Ti, $N, $V, $l) = array_map('base64_decode', explode("-", $y));
        set_password($Ti, $N, $V, decrypt_string(base64_decode($jb), $kg));
        $_SESSION["db"][$Ti][$N][$V][$l] = true;
    }
}
function unset_permanent()
{
    global $Yf;
    foreach ($Yf
             as $y => $X) {
        list($Ti, $N, $V, $l) = array_map('base64_decode', explode("-", $y));
        if ($Ti == DRIVER && $N == SERVER && $V == $_GET["username"] && $l == DB) {
            unset($Yf[$y]);
        }
    }
    cookie("adminer_permanent", implode(" ", $Yf));
}

function auth_error($n)
{
    global $b, $wd;
    $kh = session_name();
    if (isset($_GET["username"])) {
        header("HTTP/1.1 403 Forbidden");
        if (($_COOKIE[$kh] || $_GET[$kh]) && !$wd) {
            $n = lang(87);
        } else {
            restart_session();
            add_invalid_login();
            $F = get_password();
            if ($F !== null) {
                if ($F === false) {
                    $n .= '<br>' . lang(88, target_blank(), '<code>permanentLogin()</code>');
                }
                set_password(DRIVER, SERVER, $_GET["username"], null);
            }
            unset_permanent();
        }
    }
    if (!$_COOKIE[$kh] && $_GET[$kh] && ini_bool("session.use_only_cookies")) {
        $n = lang(89);
    }
    $Lf = session_get_cookie_params();
    cookie("adminer_key", ($_COOKIE["adminer_key"] ? $_COOKIE["adminer_key"] : rand_string()), $Lf["lifetime"]);
    page_header(lang(39), $n, null);
    echo "<form action='' method='post'>\n", "<div>";
    if (hidden_fields($_POST, ["auth"])) {
        echo "<p class='message'>" . lang(90) . "\n";
    }
    echo "</div>\n";
    $b->loginForm();
    echo "</form>\n";
    page_footer("auth");
    exit;
}

if (isset($_GET["username"]) && !class_exists("Min_DB")) {
    unset($_SESSION["pwds"][DRIVER]);
    unset_permanent();
    page_header(lang(91), lang(92, implode(", ", $eg)), false);
    page_footer("auth");
    exit;
}
stop_session(true);
if (isset($_GET["username"])) {
    list($Bd, $ag) = explode(":", SERVER, 2);
    if (is_numeric($ag) && $ag < 1024) {
        auth_error(lang(93));
    }
    check_invalid_login();
    $g = connect();
    $m = new
    Min_Driver($g);
}
$xe = null;
if (!is_object($g) || ($xe = $b->login($_GET["username"], get_password())) !== true) {
    auth_error((is_string($g) ? h($g) : (is_string($xe) ? $xe : lang(94))));
}
if ($La && $_POST["token"]) {
    $_POST["token"] = $ki;
}
$n = '';
if ($_POST) {
    if (!verify_token()) {
        $Pd = "max_input_vars";
        $Ie = ini_get($Pd);
        if (extension_loaded("suhosin")) {
            foreach (["suhosin.request.max_vars", "suhosin.post.max_vars"] as $y) {
                $X = ini_get($y);
                if ($X && (!$Ie || $X < $Ie)) {
                    $Pd = $y;
                    $Ie = $X;
                }
            }
        }
        $n = (!$_POST["token"] && $Ie ? lang(95, "'$Pd'") : lang(84) . ' ' . lang(96));
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    $n = lang(97, "'post_max_size'");
    if (isset($_GET["sql"])) {
        $n .= ' ' . lang(98);
    }
}
function select($H, $h = null, $_f = [], $z = 0)
{
    global $x;
    $we = [];
    $w = [];
    $e = [];
    $Ua = [];
    $zi = [];
    $I = [];
    odd('');
    for ($s = 0; (!$z || $s < $z) && ($J = $H->fetch_row()); $s++) {
        if (!$s) {
            echo "<table cellspacing='0' class='nowrap'>\n", "<thead><tr>";
            for ($be = 0; $be < count($J); $be++) {
                $o = $H->fetch_field();
                $C = $o->name;
                $zf = $o->orgtable;
                $yf = $o->orgname;
                $I[$o->table] = $zf;
                if ($_f && $x == "sql") {
                    $we[$be] = ($C == "table" ? "table=" : ($C == "possible_keys" ? "indexes=" : null));
                } elseif ($zf != "") {
                    if (!isset($w[$zf])) {
                        $w[$zf] = [];
                        foreach (indexes($zf, $h) as $v) {
                            if ($v["type"] == "PRIMARY") {
                                $w[$zf] = array_flip($v["columns"]);
                                break;
                            }
                        }
                        $e[$zf] = $w[$zf];
                    }
                    if (isset($e[$zf][$yf])) {
                        unset($e[$zf][$yf]);
                        $w[$zf][$yf] = $be;
                        $we[$be] = $zf;
                    }
                }
                if ($o->charsetnr == 63) {
                    $Ua[$be] = true;
                }
                $zi[$be] = $o->type;
                echo "<th" . ($zf != "" || $o->name != $yf ? " title='" . h(($zf != "" ? "$zf." : "") . $yf) . "'" : "") . ">" . h($C) . ($_f ? doc_link(['sql' => "explain-output.html#explain_" . strtolower($C), 'mariadb' => "explain/#the-columns-in-explain-select",]) : "");
            }
            echo "</thead>\n";
        }
        echo "<tr" . odd() . ">";
        foreach ($J
                 as $y => $X) {
            if ($X === null) {
                $X = "<i>NULL</i>";
            } elseif ($Ua[$y] && !is_utf8($X)) {
                $X = "<i>" . lang(48, strlen($X)) . "</i>";
            } else {
                $X = h($X);
                if ($zi[$y] == 254) {
                    $X = "<code>$X</code>";
                }
            }
            if (isset($we[$y]) && !$e[$we[$y]]) {
                if ($_f && $x == "sql") {
                    $R = $J[array_search("table=", $we)];
                    $_ = $we[$y] . urlencode($_f[$R] != "" ? $_f[$R] : $R);
                } else {
                    $_ = "edit=" . urlencode($we[$y]);
                    foreach ($w[$we[$y]] as $nb => $be) {
                        $_ .= "&where" . urlencode("[" . bracket_escape($nb) . "]") . "=" . urlencode($J[$be]);
                    }
                }
                $X = "<a href='" . h(ME . $_) . "'>$X</a>";
            }
            echo "<td>$X";
        }
    }
    echo($s ? "</table>" : "<p class='message'>" . lang(12)) . "\n";

    return $I;
}

function referencable_primary($eh)
{
    $I = [];
    foreach (table_status('', true) as $Lh => $R) {
        if ($Lh != $eh && fk_support($R)) {
            foreach (fields($Lh) as $o) {
                if ($o["primary"]) {
                    if ($I[$Lh]) {
                        unset($I[$Lh]);
                        break;
                    }
                    $I[$Lh] = $o;
                }
            }
        }
    }

    return $I;
}

function textarea($C, $Y, $K = 10, $sb = 80)
{
    global $x;
    echo "<textarea name='$C' rows='$K' cols='$sb' class='sqlarea jush-$x' spellcheck='false' wrap='off'>";
    if (is_array($Y)) {
        foreach ($Y
                 as $X) {
            echo
            h($X[0]) . "\n\n\n";
        }
    } else {
        echo
        h($Y);
    }
    echo "</textarea>";
}

function edit_type($y, $o, $qb, $fd = [], $Mc = [])
{
    global $Dh, $zi, $Fi, $nf;
    $U = $o["type"];
    echo '<td><select name="', h($y), '[type]" class="type" aria-labelledby="label-type">';
    if ($U && !isset($zi[$U]) && !isset($fd[$U]) && !in_array($U, $Mc)) {
        $Mc[] = $U;
    }
    if ($fd) {
        $Dh[lang(99)] = $fd;
    }
    echo
    optionlist(array_merge($Mc, $Dh), $U), '</select>
', on_help("getTarget(event).value", 1), script("mixin(qsl('select'), {onfocus: function () { lastType = selectValue(this); }, onchange: editingTypeChange});", ""), '<td><input name="', h($y), '[length]" value="', h($o["length"]), '" size="3"', (!$o["length"] && preg_match('~var(char|binary)$~', $U) ? " class='required'" : "");
    echo ' aria-labelledby="label-length">', script("mixin(qsl('input'), {onfocus: editingLengthFocus, oninput: editingLengthChange});", ""), '<td class="options">', "<select name='" . h($y) . "[collation]'" . (preg_match('~(char|text|enum|set)$~', $U) ? "" : " class='hidden'") . '><option value="">(' . lang(100) . ')' . optionlist($qb, $o["collation"]) . '</select>', ($Fi ? "<select name='" . h($y) . "[unsigned]'" . (!$U || preg_match(number_type(), $U) ? "" : " class='hidden'") . '><option>' . optionlist($Fi, $o["unsigned"]) . '</select>' : ''), (isset($o['on_update']) ? "<select name='" . h($y) . "[on_update]'" . (preg_match('~timestamp|datetime~', $U) ? "" : " class='hidden'") . '>' . optionlist(["" => "(" . lang(101) . ")", "CURRENT_TIMESTAMP"], $o["on_update"]) . '</select>' : ''), ($fd ? "<select name='" . h($y) . "[on_delete]'" . (preg_match("~`~", $U) ? "" : " class='hidden'") . "><option value=''>(" . lang(102) . ")" . optionlist(explode("|", $nf), $o["on_delete"]) . "</select> " : " ");
}

function process_length($te)
{
    global $xc;

    return (preg_match("~^\\s*\\(?\\s*$xc(?:\\s*,\\s*$xc)*+\\s*\\)?\\s*\$~", $te) && preg_match_all("~$xc~", $te, $Ce) ? "(" . implode(",", $Ce[0]) . ")" : preg_replace('~^[0-9].*~', '(\0)', preg_replace('~[^-0-9,+()[\]]~', '', $te)));
}

function process_type($o, $ob = "COLLATE")
{
    global $Fi;

    return " $o[type]" . process_length($o["length"]) . (preg_match(number_type(), $o["type"]) && in_array($o["unsigned"], $Fi) ? " $o[unsigned]" : "") . (preg_match('~char|text|enum|set~', $o["type"]) && $o["collation"] ? " $ob " . q($o["collation"]) : "");
}

function process_field($o, $xi)
{
    return
        [idf_escape(trim($o["field"])), process_type($xi), ($o["null"] ? " NULL" : " NOT NULL"), default_value($o), (preg_match('~timestamp|datetime~', $o["type"]) && $o["on_update"] ? " ON UPDATE $o[on_update]" : ""), (support("comment") && $o["comment"] != "" ? " COMMENT " . q($o["comment"]) : ""), ($o["auto_increment"] ? auto_increment() : null),];
}

function default_value($o)
{
    $Tb = $o["default"];

    return ($Tb === null ? "" : " DEFAULT " . (preg_match('~char|binary|text|enum|set~', $o["type"]) || preg_match('~^(?![a-z])~i', $Tb) ? q($Tb) : $Tb));
}

function type_class($U)
{
    foreach (['char' => 'text', 'date' => 'time|year', 'binary' => 'blob', 'enum' => 'set',] as $y => $X) {
        if (preg_match("~$y|$X~", $U)) {
            return " class='$y'";
        }
    }
}

function edit_fields($p, $qb, $U = "TABLE", $fd = [], $wb = false)
{
    global $Qd;
    $p = array_values($p);
    echo '<thead><tr>
';
    if ($U == "PROCEDURE") {
        echo '<td>';
    }
    echo '<th id="label-name">', ($U == "TABLE" ? lang(103) : lang(104)), '<td id="label-type">', lang(50), '<textarea id="enum-edit" rows="4" cols="12" wrap="off" style="display: none;"></textarea>', script("qs('#enum-edit').onblur = editingLengthBlur;"), '<td id="label-length">', lang(105), '<td>', lang(106);
    if ($U == "TABLE") {
        echo '<td id="label-null">NULL
<td><input type="radio" name="auto_increment_col" value=""><acronym id="label-ai" title="', lang(52), '">AI</acronym>', doc_link(['sql' => "example-auto-increment.html", 'mariadb' => "auto_increment/", 'sqlite' => "autoinc.html", 'pgsql' => "datatype.html#DATATYPE-SERIAL", 'mssql' => "ms186775.aspx",]), '<td id="label-default">', lang(53), (support("comment") ? "<td id='label-comment'" . ($wb ? "" : " class='hidden'") . ">" . lang(51) : "");
    }
    echo '<td>', "<input type='image' class='icon' name='add[" . (support("move_col") ? 0 : count($p)) . "]' src='" . h(preg_replace("~\\?.*~", "", ME) . "?file=plus.gif&version=4.6.3") . "' alt='+' title='" . lang(107) . "'>" . script("row_count = " . count($p) . ";"), '</thead>
<tbody>
', script("mixin(qsl('tbody'), {onclick: editingClick, onkeydown: editingKeydown, oninput: editingInput});");
    foreach ($p
             as $s => $o) {
        $s++;
        $Af = $o[($_POST ? "orig" : "field")];
        $bc = (isset($_POST["add"][$s - 1]) || (isset($o["field"]) && !$_POST["drop_col"][$s])) && (support("drop_col") || $Af == "");
        echo '<tr', ($bc ? "" : " style='display: none;'"), '>
', ($U == "PROCEDURE" ? "<td>" . html_select("fields[$s][inout]", explode("|", $Qd), $o["inout"]) : ""), '<th>';
        if ($bc) {
            echo '<input name="fields[', $s, '][field]" value="', h($o["field"]), '" maxlength="64" autocapitalize="off" aria-labelledby="label-name">', script("qsl('input').oninput = function () { editingNameChange.call(this);" . ($o["field"] != "" || count($p) > 1 ? "" : " editingAddRow.call(this);") . " };", "");
        }
        echo '<input type="hidden" name="fields[', $s, '][orig]" value="', h($Af), '">
';
        edit_type("fields[$s]", $o, $qb, $fd);
        if ($U == "TABLE") {
            echo '<td>', checkbox("fields[$s][null]", 1, $o["null"], "", "", "block", "label-null"), '<td><label class="block"><input type="radio" name="auto_increment_col" value="', $s, '"';
            if ($o["auto_increment"]) {
                echo ' checked';
            }
            echo ' aria-labelledby="label-ai"></label><td>', checkbox("fields[$s][has_default]", 1, $o["has_default"], "", "", "", "label-default"), '<input name="fields[', $s, '][default]" value="', h($o["default"]), '" aria-labelledby="label-default">', (support("comment") ? "<td" . ($wb ? "" : " class='hidden'") . "><input name='fields[$s][comment]' value='" . h($o["comment"]) . "' maxlength='" . (min_version(5.5) ? 1024 : 255) . "' aria-labelledby='label-comment'>" : "");
        }
        echo "<td>", (support("move_col") ? "<input type='image' class='icon' name='add[$s]' src='" . h(preg_replace("~\\?.*~", "", ME) . "?file=plus.gif&version=4.6.3") . "' alt='+' title='" . lang(107) . "'> " . "<input type='image' class='icon' name='up[$s]' src='" . h(preg_replace("~\\?.*~", "", ME) . "?file=up.gif&version=4.6.3") . "' alt='↑' title='" . lang(108) . "'> " . "<input type='image' class='icon' name='down[$s]' src='" . h(preg_replace("~\\?.*~", "", ME) . "?file=down.gif&version=4.6.3") . "' alt='↓' title='" . lang(109) . "'> " : ""), ($Af == "" || support("drop_col") ? "<input type='image' class='icon' name='drop_col[$s]' src='" . h(preg_replace("~\\?.*~", "", ME) . "?file=cross.gif&version=4.6.3") . "' alt='x' title='" . lang(110) . "'>" : "");
    }
}

function process_fields(&$p)
{
    $D = 0;
    if ($_POST["up"]) {
        $ne = 0;
        foreach ($p
                 as $y => $o) {
            if (key($_POST["up"]) == $y) {
                unset($p[$y]);
                array_splice($p, $ne, 0, [$o]);
                break;
            }
            if (isset($o["field"])) {
                $ne = $D;
            }
            $D++;
        }
    } elseif ($_POST["down"]) {
        $hd = false;
        foreach ($p
                 as $y => $o) {
            if (isset($o["field"]) && $hd) {
                unset($p[key($_POST["down"])]);
                array_splice($p, $D, 0, [$hd]);
                break;
            }
            if (key($_POST["down"]) == $y) {
                $hd = $o;
            }
            $D++;
        }
    } elseif ($_POST["add"]) {
        $p = array_values($p);
        array_splice($p, key($_POST["add"]), 0, [[]]);
    } elseif (!$_POST["drop_col"]) {
        return
        false;
    }

    return
        true;
}

function normalize_enum($B)
{
    return "'" . str_replace("'", "''", addcslashes(stripcslashes(str_replace($B[0][0] . $B[0][0], $B[0][0], substr($B[0], 1, -1))), '\\')) . "'";
}

function grant($md, $mg, $e, $mf)
{
    if (!$mg) {
        return
        true;
    }
    if ($mg == ["ALL PRIVILEGES", "GRANT OPTION"]) {
        return ($md == "GRANT" ? queries("$md ALL PRIVILEGES$mf WITH GRANT OPTION") : queries("$md ALL PRIVILEGES$mf") && queries("$md GRANT OPTION$mf"));
    }

    return
        queries("$md " . preg_replace('~(GRANT OPTION)\([^)]*\)~', '\1', implode("$e, ", $mg) . $e) . $mf);
}

function drop_create($gc, $i, $hc, $Xh, $jc, $A, $Ne, $Le, $Me, $jf, $Ye)
{
    if ($_POST["drop"]) {
        query_redirect($gc, $A, $Ne);
    } elseif ($jf == "") {
        query_redirect($i, $A, $Me);
    } elseif ($jf != $Ye) {
        $Gb = queries($i);
        queries_redirect($A, $Le, $Gb && queries($gc));
        if ($Gb) {
            queries($hc);
        }
    } else {
        queries_redirect($A, $Le, queries($Xh) && queries($jc) && queries($gc) && queries($i));
    }
}

function create_trigger($mf, $J)
{
    global $x;
    $ci = " $J[Timing] $J[Event]" . ($J["Event"] == "UPDATE OF" ? " " . idf_escape($J["Of"]) : "");

    return "CREATE TRIGGER " . idf_escape($J["Trigger"]) . ($x == "mssql" ? $mf . $ci : $ci . $mf) . rtrim(" $J[Type]\n$J[Statement]", ";") . ";";
}

function create_routine($Rg, $J)
{
    global $Qd, $x;
    $O = [];
    $p = (array)$J["fields"];
    ksort($p);
    foreach ($p
             as $o) {
        if ($o["field"] != "") {
            $O[] = (preg_match("~^($Qd)\$~", $o["inout"]) ? "$o[inout] " : "") . idf_escape($o["field"]) . process_type($o, "CHARACTER SET");
        }
    }
    $Ub = rtrim("\n$J[definition]", ";");

    return "CREATE $Rg " . idf_escape(trim($J["name"])) . " (" . implode(", ", $O) . ")" . (isset($_GET["function"]) ? " RETURNS" . process_type($J["returns"], "CHARACTER SET") : "") . ($J["language"] ? " LANGUAGE $J[language]" : "") . ($x == "pgsql" ? " AS " . q($Ub) : "$Ub;");
}

function remove_definer($G)
{
    return
        preg_replace('~^([A-Z =]+) DEFINER=`' . preg_replace('~@(.*)~', '`@`(%|\1)', logged_user()) . '`~', '\1', $G);
}

function format_foreign_key($q)
{
    global $nf;

    return " FOREIGN KEY (" . implode(", ", array_map('idf_escape', $q["source"])) . ") REFERENCES " . table($q["table"]) . " (" . implode(", ", array_map('idf_escape', $q["target"])) . ")" . (preg_match("~^($nf)\$~", $q["on_delete"]) ? " ON DELETE $q[on_delete]" : "") . (preg_match("~^($nf)\$~", $q["on_update"]) ? " ON UPDATE $q[on_update]" : "");
}

function tar_file($Vc, $hi)
{
    $I = pack("a100a8a8a8a12a12", $Vc, 644, 0, 0, decoct($hi->size), decoct(time()));
    $hb = 8 * 32;
    for ($s = 0; $s < strlen($I); $s++) {
        $hb += ord($I[$s]);
    }
    $I .= sprintf("%06o", $hb) . "\0 ";
    echo $I, str_repeat("\0", 512 - strlen($I));
    $hi->send();
    echo
    str_repeat("\0", 511 - ($hi->size + 511) % 512);
}

function ini_bytes($Pd)
{
    $X = ini_get($Pd);
    switch (strtolower(substr($X, -1))) {
        case'g':
            $X *= 1024;
            // no break
        case'm':
            $X *= 1024;
            // no break
        case'k':
            $X *= 1024;
    }

    return $X;
}

function doc_link($Wf, $Yh = "<sup>?</sup>")
{
    global $x, $g;
    $ih = $g->server_info;
    $Ui = preg_replace('~^(\d\.?\d).*~s', '\1', $ih);
    $Ki = ['sql' => "https://dev.mysql.com/doc/refman/$Ui/en/", 'sqlite' => "https://www.sqlite.org/", 'pgsql' => "https://www.postgresql.org/docs/$Ui/static/", 'mssql' => "https://msdn.microsoft.com/library/", 'oracle' => "https://download.oracle.com/docs/cd/B19306_01/server.102/b14200/",];
    if (preg_match('~MariaDB~', $ih)) {
        $Ki['sql'] = "https://mariadb.com/kb/en/library/";
        $Wf['sql'] = (isset($Wf['mariadb']) ? $Wf['mariadb'] : str_replace(".html", "/", $Wf['sql']));
    }

    return ($Wf[$x] ? "<a href='$Ki[$x]$Wf[$x]'" . target_blank() . ">$Yh</a>" : "");
}

function ob_gzencode($Q)
{
    return
        gzencode($Q);
}

function db_size($l)
{
    global $g;
    if (!$g->select_db($l)) {
        return "?";
    }
    $I = 0;
    foreach (table_status() as $S) {
        $I += $S["Data_length"] + $S["Index_length"];
    }

    return
        format_number($I);
}

function set_utf8mb4($i)
{
    global $g;
    static $O = false;
    if (!$O && preg_match('~\butf8mb4~i', $i)) {
        $O = true;
        echo "SET NAMES " . charset($g) . ";\n\n";
    }
}

function connect_error()
{
    global $b, $g, $ki, $n, $fc;
    if (DB != "") {
        header("HTTP/1.1 404 Not Found");
        page_header(lang(38) . ": " . h(DB), lang(111), true);
    } else {
        if ($_POST["db"] && !$n) {
            queries_redirect(substr(ME, 0, -1), lang(112), drop_databases($_POST["db"]));
        }
        page_header(lang(113), $n, false);
        echo "<p class='links'>\n";
        foreach (['database' => lang(114), 'privileges' => lang(72), 'processlist' => lang(115), 'variables' => lang(116), 'status' => lang(117),] as $y => $X) {
            if (support($y)) {
                echo "<a href='" . h(ME) . "$y='>$X</a>\n";
            }
        }
        echo "<p>" . lang(118, $fc[DRIVER], "<b>" . h($g->server_info) . "</b>", "<b>$g->extension</b>") . "\n", "<p>" . lang(119, "<b>" . h(logged_user()) . "</b>") . "\n";
        $k = $b->databases();
        if ($k) {
            $Yg = support("scheme");
            $qb = collations();
            echo "<form action='' method='post'>\n", "<table cellspacing='0' class='checkable'>\n", script("mixin(qsl('table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true)});"), "<thead><tr>" . (support("database") ? "<td>" : "") . "<th>" . lang(38) . " - <a href='" . h(ME) . "refresh=1'>" . lang(120) . "</a>" . "<td>" . lang(121) . "<td>" . lang(122) . "<td>" . lang(123) . " - <a href='" . h(ME) . "dbsize=1'>" . lang(124) . "</a>" . script("qsl('a').onclick = partial(ajaxSetHtml, '" . js_escape(ME) . "script=connect');", "") . "</thead>\n";
            $k = ($_GET["dbsize"] ? count_tables($k) : array_flip($k));
            foreach ($k
                     as $l => $T) {
                $Qg = h(ME) . "db=" . urlencode($l);
                $t = h("Db-" . $l);
                echo "<tr" . odd() . ">" . (support("database") ? "<td>" . checkbox("db[]", $l, in_array($l, (array)$_POST["db"]), "", "", "", $t) : ""), "<th><a href='$Qg' id='$t'>" . h($l) . "</a>";
                $pb = h(db_collation($l, $qb));
                echo "<td>" . (support("database") ? "<a href='$Qg" . ($Yg ? "&amp;ns=" : "") . "&amp;database=' title='" . lang(68) . "'>$pb</a>" : $pb), "<td align='right'><a href='$Qg&amp;schema=' id='tables-" . h($l) . "' title='" . lang(71) . "'>" . ($_GET["dbsize"] ? $T : "?") . "</a>", "<td align='right' id='size-" . h($l) . "'>" . ($_GET["dbsize"] ? db_size($l) : "?"), "\n";
            }
            echo "</table>\n", (support("database") ? "<div class='footer'><div>\n" . "<fieldset><legend>" . lang(125) . " <span id='selected'></span></legend><div>\n" . "<input type='hidden' name='all' value=''>" . script("qsl('input').onclick = function () { selectCount('selected', formChecked(this, /^db/)); };") . "<input type='submit' name='drop' value='" . lang(126) . "'>" . confirm() . "\n" . "</div></fieldset>\n" . "</div></div>\n" : ""), "<input type='hidden' name='token' value='$ki'>\n", "</form>\n", script("tableCheck();");
        }
    }
    page_footer("db");
}

if (isset($_GET["status"])) {
    $_GET["variables"] = $_GET["status"];
}
if (isset($_GET["import"])) {
    $_GET["sql"] = $_GET["import"];
}
if (!(DB != "" ? $g->select_db(DB) : isset($_GET["sql"]) || isset($_GET["dump"]) || isset($_GET["database"]) || isset($_GET["processlist"]) || isset($_GET["privileges"]) || isset($_GET["user"]) || isset($_GET["variables"]) || $_GET["script"] == "connect" || $_GET["script"] == "kill")) {
    if (DB != "" || $_GET["refresh"]) {
        restart_session();
        set_session("dbs", null);
    }
    connect_error();
    exit;
}
if (support("scheme") && DB != "" && $_GET["ns"] !== "") {
    if (!isset($_GET["ns"])) {
        redirect(preg_replace('~ns=[^&]*&~', '', ME) . "ns=" . get_schema());
    }
    if (!set_schema($_GET["ns"])) {
        header("HTTP/1.1 404 Not Found");
        page_header(lang(78) . ": " . h($_GET["ns"]), lang(127), true);
        page_footer("ns");
        exit;
    }
}
$nf = "RESTRICT|NO ACTION|CASCADE|SET NULL|SET DEFAULT";

class TmpFile
{
    public $handler;

    public $size;

    public function __construct()
    {
        $this->handler = tmpfile();
    }

    public function write($Ab)
    {
        $this->size += strlen($Ab);
        fwrite($this->handler, $Ab);
    }

    public function send()
    {
        fseek($this->handler, 0);
        fpassthru($this->handler);
        fclose($this->handler);
    }
}

$xc = "'(?:''|[^'\\\\]|\\\\.)*'";
$Qd = "IN|OUT|INOUT";
if (isset($_GET["select"]) && ($_POST["edit"] || $_POST["clone"]) && !$_POST["save"]) {
    $_GET["edit"] = $_GET["select"];
}
if (isset($_GET["callf"])) {
    $_GET["call"] = $_GET["callf"];
}
if (isset($_GET["function"])) {
    $_GET["procedure"] = $_GET["function"];
}
if (isset($_GET["download"])) {
    $a = $_GET["download"];
    $p = fields($a);
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=" . friendly_url("$a-" . implode("_", $_GET["where"])) . "." . friendly_url($_GET["field"]));
    $L = [idf_escape($_GET["field"])];
    $H = $m->select($a, $L, [where($_GET, $p)], $L);
    $J = ($H ? $H->fetch_row() : []);
    echo $m->value($J[0], $p[$_GET["field"]]);
    exit;
} elseif (isset($_GET["table"])) {
    $a = $_GET["table"];
    $p = fields($a);
    if (!$p) {
        $n = error();
    }
    $S = table_status1($a, true);
    $C = $b->tableName($S);
    page_header(($p && is_view($S) ? $S['Engine'] == 'materialized view' ? lang(128) : lang(129) : lang(130)) . ": " . ($C != "" ? $C : h($a)), $n);
    $b->selectLinks($S);
    $vb = $S["Comment"];
    if ($vb != "") {
        echo "<p class='nowrap'>" . lang(51) . ": " . h($vb) . "\n";
    }
    if ($p) {
        $b->tableStructurePrint($p);
    }
    if (!is_view($S)) {
        if (support("indexes")) {
            echo "<h3 id='indexes'>" . lang(131) . "</h3>\n";
            $w = indexes($a);
            if ($w) {
                $b->tableIndexesPrint($w);
            }
            echo '<p class="links"><a href="' . h(ME) . 'indexes=' . urlencode($a) . '">' . lang(132) . "</a>\n";
        }
        if (fk_support($S)) {
            echo "<h3 id='foreign-keys'>" . lang(99) . "</h3>\n";
            $fd = foreign_keys($a);
            if ($fd) {
                echo "<table cellspacing='0'>\n", "<thead><tr><th>" . lang(133) . "<td>" . lang(134) . "<td>" . lang(102) . "<td>" . lang(101) . "<td></thead>\n";
                foreach ($fd
                         as $C => $q) {
                    echo "<tr title='" . h($C) . "'>", "<th><i>" . implode("</i>, <i>", array_map('h', $q["source"])) . "</i>", "<td><a href='" . h($q["db"] != "" ? preg_replace('~db=[^&]*~', "db=" . urlencode($q["db"]), ME) : ($q["ns"] != "" ? preg_replace('~ns=[^&]*~', "ns=" . urlencode($q["ns"]), ME) : ME)) . "table=" . urlencode($q["table"]) . "'>" . ($q["db"] != "" ? "<b>" . h($q["db"]) . "</b>." : "") . ($q["ns"] != "" ? "<b>" . h($q["ns"]) . "</b>." : "") . h($q["table"]) . "</a>", "(<i>" . implode("</i>, <i>", array_map('h', $q["target"])) . "</i>)", "<td>" . h($q["on_delete"]) . "\n", "<td>" . h($q["on_update"]) . "\n", '<td><a href="' . h(ME . 'foreign=' . urlencode($a) . '&name=' . urlencode($C)) . '">' . lang(135) . '</a>';
                }
                echo "</table>\n";
            }
            echo '<p class="links"><a href="' . h(ME) . 'foreign=' . urlencode($a) . '">' . lang(136) . "</a>\n";
        }
    }
    if (support(is_view($S) ? "view_trigger" : "trigger")) {
        echo "<h3 id='triggers'>" . lang(137) . "</h3>\n";
        $wi = triggers($a);
        if ($wi) {
            echo "<table cellspacing='0'>\n";
            foreach ($wi
                     as $y => $X) {
                echo "<tr valign='top'><td>" . h($X[0]) . "<td>" . h($X[1]) . "<th>" . h($y) . "<td><a href='" . h(ME . 'trigger=' . urlencode($a) . '&name=' . urlencode($y)) . "'>" . lang(135) . "</a>\n";
            }
            echo "</table>\n";
        }
        echo '<p class="links"><a href="' . h(ME) . 'trigger=' . urlencode($a) . '">' . lang(138) . "</a>\n";
    }
} elseif (isset($_GET["schema"])) {
    page_header(lang(71), "", [], h(DB . ($_GET["ns"] ? ".$_GET[ns]" : "")));
    $Nh = [];
    $Oh = [];
    $ea = ($_GET["schema"] ? $_GET["schema"] : $_COOKIE["adminer_schema-" . str_replace(".", "_", DB)]);
    preg_match_all('~([^:]+):([-0-9.]+)x([-0-9.]+)(_|$)~', $ea, $Ce, PREG_SET_ORDER);
    foreach ($Ce
             as $s => $B) {
        $Nh[$B[1]] = [$B[2], $B[3]];
        $Oh[] = "\n\t'" . js_escape($B[1]) . "': [ $B[2], $B[3] ]";
    }
    $li = 0;
    $Ra = -1;
    $Xg = [];
    $Cg = [];
    $re = [];
    foreach (table_status('', true) as $R => $S) {
        if (is_view($S)) {
            continue;
        }
        $bg = 0;
        $Xg[$R]["fields"] = [];
        foreach (fields($R) as $C => $o) {
            $bg += 1.25;
            $o["pos"] = $bg;
            $Xg[$R]["fields"][$C] = $o;
        }
        $Xg[$R]["pos"] = ($Nh[$R] ? $Nh[$R] : [$li, 0]);
        foreach ($b->foreignKeys($R) as $X) {
            if (!$X["db"]) {
                $pe = $Ra;
                if ($Nh[$R][1] || $Nh[$X["table"]][1]) {
                    $pe = min(floatval($Nh[$R][1]), floatval($Nh[$X["table"]][1])) - 1;
                } else {
                    $Ra -= .1;
                }
                while ($re[(string)$pe]) {
                    $pe -= .0001;
                }
                $Xg[$R]["references"][$X["table"]][(string)$pe] = [$X["source"], $X["target"]];
                $Cg[$X["table"]][$R][(string)$pe] = $X["target"];
                $re[(string)$pe] = true;
            }
        }
        $li = max($li, $Xg[$R]["pos"][0] + 2.5 + $bg);
    }
    echo '<div id="schema" style="height: ', $li, 'em;">
<script', nonce(), '>
qs(\'#schema\').onselectstart = function () { return false; };
var tablePos = {', implode(",", $Oh) . "\n", '};
var em = qs(\'#schema\').offsetHeight / ', $li, ';
document.onmousemove = schemaMousemove;
document.onmouseup = partialArg(schemaMouseup, \'', js_escape(DB), '\');
</script>
';
    foreach ($Xg
             as $C => $R) {
        echo "<div class='table' style='top: " . $R["pos"][0] . "em; left: " . $R["pos"][1] . "em;'>", '<a href="' . h(ME) . 'table=' . urlencode($C) . '"><b>' . h($C) . "</b></a>", script("qsl('div').onmousedown = schemaMousedown;");
        foreach ($R["fields"] as $o) {
            $X = '<span' . type_class($o["type"]) . ' title="' . h($o["full_type"] . ($o["null"] ? " NULL" : '')) . '">' . h($o["field"]) . '</span>';
            echo "<br>" . ($o["primary"] ? "<i>$X</i>" : $X);
        }
        foreach ((array)$R["references"] as $Uh => $Dg) {
            foreach ($Dg
                     as $pe => $_g) {
                $qe = $pe - $Nh[$C][1];
                $s = 0;
                foreach ($_g[0] as $sh) {
                    echo "\n<div class='references' title='" . h($Uh) . "' id='refs$pe-" . ($s++) . "' style='left: $qe" . "em; top: " . $R["fields"][$sh]["pos"] . "em; padding-top: .5em;'><div style='border-top: 1px solid Gray; width: " . (-$qe) . "em;'></div></div>";
                }
            }
        }
        foreach ((array)$Cg[$C] as $Uh => $Dg) {
            foreach ($Dg
                     as $pe => $e) {
                $qe = $pe - $Nh[$C][1];
                $s = 0;
                foreach ($e
                         as $Th) {
                    echo "\n<div class='references' title='" . h($Uh) . "' id='refd$pe-" . ($s++) . "' style='left: $qe" . "em; top: " . $R["fields"][$Th]["pos"] . "em; height: 1.25em; background: url(" . h(preg_replace("~\\?.*~", "", ME) . "?file=arrow.gif) no-repeat right center;&version=4.6.3") . "'><div style='height: .5em; border-bottom: 1px solid Gray; width: " . (-$qe) . "em;'></div></div>";
                }
            }
        }
        echo "\n</div>\n";
    }
    foreach ($Xg
             as $C => $R) {
        foreach ((array)$R["references"] as $Uh => $Dg) {
            foreach ($Dg
                     as $pe => $_g) {
                $Re = $li;
                $Ge = -10;
                foreach ($_g[0] as $y => $sh) {
                    $cg = $R["pos"][0] + $R["fields"][$sh]["pos"];
                    $dg = $Xg[$Uh]["pos"][0] + $Xg[$Uh]["fields"][$_g[1][$y]]["pos"];
                    $Re = min($Re, $cg, $dg);
                    $Ge = max($Ge, $cg, $dg);
                }
                echo "<div class='references' id='refl$pe' style='left: $pe" . "em; top: $Re" . "em; padding: .5em 0;'><div style='border-right: 1px solid Gray; margin-top: 1px; height: " . ($Ge - $Re) . "em;'></div></div>\n";
            }
        }
    }
    echo '</div>
<p class="links"><a href="', h(ME . "schema=" . urlencode($ea)), '" id="schema-link">', lang(139), '</a>
';
} elseif (isset($_GET["dump"])) {
    $a = $_GET["dump"];
    if ($_POST && !$n) {
        $Db = "";
        foreach (["output", "format", "db_style", "routines", "events", "table_style", "auto_increment", "triggers", "data_style"] as $y) {
            $Db .= "&$y=" . urlencode($_POST[$y]);
        }
        cookie("adminer_export", substr($Db, 1));
        $T = array_flip((array)$_POST["tables"]) + array_flip((array)$_POST["data"]);
        $Jc = dump_headers((count($T) == 1 ? key($T) : DB), (DB == "" || count($T) > 1));
        $Yd = preg_match('~sql~', $_POST["format"]);
        if ($Yd) {
            echo "-- Adminer $ia " . $fc[DRIVER] . " dump\n\n";
            if ($x == "sql") {
                echo "SET NAMES utf8;
SET time_zone = '+00:00';
" . ($_POST["data_style"] ? "SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
" : "") . "
";
                $g->query("SET time_zone = '+00:00';");
            }
        }
        $Eh = $_POST["db_style"];
        $k = [DB];
        if (DB == "") {
            $k = $_POST["databases"];
            if (is_string($k)) {
                $k = explode("\n", rtrim(str_replace("\r", "", $k), "\n"));
            }
        }
        foreach ((array)$k
                 as $l) {
            $b->dumpDatabase($l);
            if ($g->select_db($l)) {
                if ($Yd && preg_match('~CREATE~', $Eh) && ($i = $g->result("SHOW CREATE DATABASE " . idf_escape($l), 1))) {
                    set_utf8mb4($i);
                    if ($Eh == "DROP+CREATE") {
                        echo "DROP DATABASE IF EXISTS " . idf_escape($l) . ";\n";
                    }
                    echo "$i;\n";
                }
                if ($Yd) {
                    if ($Eh) {
                        echo
                        use_sql($l) . ";\n\n";
                    }
                    $Gf = "";
                    if ($_POST["routines"]) {
                        foreach (["FUNCTION", "PROCEDURE"] as $Rg) {
                            foreach (get_rows("SHOW $Rg STATUS WHERE Db = " . q($l), null, "-- ") as $J) {
                                $i = remove_definer($g->result("SHOW CREATE $Rg " . idf_escape($J["Name"]), 2));
                                set_utf8mb4($i);
                                $Gf .= ($Eh != 'DROP+CREATE' ? "DROP $Rg IF EXISTS " . idf_escape($J["Name"]) . ";;\n" : "") . "$i;;\n\n";
                            }
                        }
                    }
                    if ($_POST["events"]) {
                        foreach (get_rows("SHOW EVENTS", null, "-- ") as $J) {
                            $i = remove_definer($g->result("SHOW CREATE EVENT " . idf_escape($J["Name"]), 3));
                            set_utf8mb4($i);
                            $Gf .= ($Eh != 'DROP+CREATE' ? "DROP EVENT IF EXISTS " . idf_escape($J["Name"]) . ";;\n" : "") . "$i;;\n\n";
                        }
                    }
                    if ($Gf) {
                        echo "DELIMITER ;;\n\n$Gf" . "DELIMITER ;\n\n";
                    }
                }
                if ($_POST["table_style"] || $_POST["data_style"]) {
                    $Wi = [];
                    foreach (table_status('', true) as $C => $S) {
                        $R = (DB == "" || in_array($C, (array)$_POST["tables"]));
                        $Mb = (DB == "" || in_array($C, (array)$_POST["data"]));
                        if ($R || $Mb) {
                            if ($Jc == "tar") {
                                $hi = new
                                TmpFile;
                                ob_start([$hi, 'write'], 1e5);
                            }
                            $b->dumpTable($C, ($R ? $_POST["table_style"] : ""), (is_view($S) ? 2 : 0));
                            if (is_view($S)) {
                                $Wi[] = $C;
                            } elseif ($Mb) {
                                $p = fields($C);
                                $b->dumpData($C, $_POST["data_style"], "SELECT *" . convert_fields($p, $p) . " FROM " . table($C));
                            }
                            if ($Yd && $_POST["triggers"] && $R && ($wi = trigger_sql($C))) {
                                echo "\nDELIMITER ;;\n$wi\nDELIMITER ;\n";
                            }
                            if ($Jc == "tar") {
                                ob_end_flush();
                                tar_file((DB != "" ? "" : "$l/") . "$C.csv", $hi);
                            } elseif ($Yd) {
                                echo "\n";
                            }
                        }
                    }
                    foreach ($Wi
                             as $Vi) {
                        $b->dumpTable($Vi, $_POST["table_style"], 1);
                    }
                    if ($Jc == "tar") {
                        echo
                    pack("x512");
                    }
                }
            }
        }
        if ($Yd) {
            echo "-- " . $g->result("SELECT NOW()") . "\n";
        }
        exit;
    }
    page_header(lang(74), $n, ($_GET["export"] != "" ? ["table" => $_GET["export"]] : []), h(DB));
    echo '
<form action="" method="post">
<table cellspacing="0">
';
    $Qb = ['', 'USE', 'DROP+CREATE', 'CREATE'];
    $Ph = ['', 'DROP+CREATE', 'CREATE'];
    $Nb = ['', 'TRUNCATE+INSERT', 'INSERT'];
    if ($x == "sql") {
        $Nb[] = 'INSERT+UPDATE';
    }
    parse_str($_COOKIE["adminer_export"], $J);
    if (!$J) {
        $J = ["output" => "text", "format" => "sql", "db_style" => (DB != "" ? "" : "CREATE"), "table_style" => "DROP+CREATE", "data_style" => "INSERT"];
    }
    if (!isset($J["events"])) {
        $J["routines"] = $J["events"] = ($_GET["dump"] == "");
        $J["triggers"] = $J["table_style"];
    }
    echo "<tr><th>" . lang(140) . "<td>" . html_select("output", $b->dumpOutput(), $J["output"], 0) . "\n";
    echo "<tr><th>" . lang(141) . "<td>" . html_select("format", $b->dumpFormat(), $J["format"], 0) . "\n";
    echo($x == "sqlite" ? "" : "<tr><th>" . lang(38) . "<td>" . html_select('db_style', $Qb, $J["db_style"]) . (support("routine") ? checkbox("routines", 1, $J["routines"], lang(142)) : "") . (support("event") ? checkbox("events", 1, $J["events"], lang(143)) : "")), "<tr><th>" . lang(122) . "<td>" . html_select('table_style', $Ph, $J["table_style"]) . checkbox("auto_increment", 1, $J["auto_increment"], lang(52)) . (support("trigger") ? checkbox("triggers", 1, $J["triggers"], lang(137)) : ""), "<tr><th>" . lang(144) . "<td>" . html_select('data_style', $Nb, $J["data_style"]), '</table>
<p><input type="submit" value="', lang(74), '">
<input type="hidden" name="token" value="', $ki, '">

<table cellspacing="0">
', script("qsl('table').onclick = dumpClick;");
    $gg = [];
    if (DB != "") {
        $fb = ($a != "" ? "" : " checked");
        echo "<thead><tr>", "<th style='text-align: left;'><label class='block'><input type='checkbox' id='check-tables'$fb>" . lang(122) . "</label>" . script("qs('#check-tables').onclick = partial(formCheck, /^tables\\[/);", ""), "<th style='text-align: right;'><label class='block'>" . lang(144) . "<input type='checkbox' id='check-data'$fb></label>" . script("qs('#check-data').onclick = partial(formCheck, /^data\\[/);", ""), "</thead>\n";
        $Wi = "";
        $Qh = tables_list();
        foreach ($Qh
                 as $C => $U) {
            $fg = preg_replace('~_.*~', '', $C);
            $fb = ($a == "" || $a == (substr($a, -1) == "%" ? "$fg%" : $C));
            $jg = "<tr><td>" . checkbox("tables[]", $C, $fb, $C, "", "block");
            if ($U !== null && !preg_match('~table~i', $U)) {
                $Wi .= "$jg\n";
            } else {
                echo "$jg<td align='right'><label class='block'><span id='Rows-" . h($C) . "'></span>" . checkbox("data[]", $C, $fb) . "</label>\n";
            }
            $gg[$fg]++;
        }
        echo $Wi;
        if ($Qh) {
            echo
        script("ajaxSetHtml('" . js_escape(ME) . "script=db');");
        }
    } else {
        echo "<thead><tr><th style='text-align: left;'>", "<label class='block'><input type='checkbox' id='check-databases'" . ($a == "" ? " checked" : "") . ">" . lang(38) . "</label>", script("qs('#check-databases').onclick = partial(formCheck, /^databases\\[/);", ""), "</thead>\n";
        $k = $b->databases();
        if ($k) {
            foreach ($k
                     as $l) {
                if (!information_schema($l)) {
                    $fg = preg_replace('~_.*~', '', $l);
                    echo "<tr><td>" . checkbox("databases[]", $l, $a == "" || $a == "$fg%", $l, "", "block") . "\n";
                    $gg[$fg]++;
                }
            }
        } else {
            echo "<tr><td><textarea name='databases' rows='10' cols='20'></textarea>";
        }
    }
    echo '</table>
</form>
';
    $Xc = true;
    foreach ($gg
             as $y => $X) {
        if ($y != "" && $X > 1) {
            echo($Xc ? "<p>" : " ") . "<a href='" . h(ME) . "dump=" . urlencode("$y%") . "'>" . h($y) . "</a>";
            $Xc = false;
        }
    }
} elseif (isset($_GET["privileges"])) {
    page_header(lang(72));
    echo '<p class="links"><a href="' . h(ME) . 'user=">' . lang(145) . "</a>";
    $H = $g->query("SELECT User, Host FROM mysql." . (DB == "" ? "user" : "db WHERE " . q(DB) . " LIKE Db") . " ORDER BY Host, User");
    $md = $H;
    if (!$H) {
        $H = $g->query("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', 1) AS User, SUBSTRING_INDEX(CURRENT_USER, '@', -1) AS Host");
    }
    echo "<form action=''><p>\n";
    hidden_fields_get();
    echo "<input type='hidden' name='db' value='" . h(DB) . "'>\n", ($md ? "" : "<input type='hidden' name='grant' value=''>\n"), "<table cellspacing='0'>\n", "<thead><tr><th>" . lang(36) . "<th>" . lang(35) . "<th></thead>\n";
    while ($J = $H->fetch_assoc()) {
        echo '<tr' . odd() . '><td>' . h($J["User"]) . "<td>" . h($J["Host"]) . '<td><a href="' . h(ME . 'user=' . urlencode($J["User"]) . '&host=' . urlencode($J["Host"])) . '">' . lang(10) . "</a>\n";
    }
    if (!$md || DB != "") {
        echo "<tr" . odd() . "><td><input name='user' autocapitalize='off'><td><input name='host' value='localhost' autocapitalize='off'><td><input type='submit' value='" . lang(10) . "'>\n";
    }
    echo "</table>\n", "</form>\n";
} elseif (isset($_GET["sql"])) {
    if (!$n && $_POST["export"]) {
        dump_headers("sql");
        $b->dumpTable("", "");
        $b->dumpData("", "table", $_POST["query"]);
        exit;
    }
    restart_session();
    $_d =& get_session("queries");
    $zd =& $_d[DB];
    if (!$n && $_POST["clear"]) {
        $zd = [];
        redirect(remove_from_uri("history"));
    }
    page_header((isset($_GET["import"]) ? lang(73) : lang(65)), $n);
    if (!$n && $_POST) {
        $jd = false;
        if (!isset($_GET["import"])) {
            $G = $_POST["query"];
        } elseif ($_POST["webfile"]) {
            $wh = $b->importServerPath();
            $jd = @fopen((file_exists($wh) ? $wh : "compress.zlib://$wh.gz"), "rb");
            $G = ($jd ? fread($jd, 1e6) : false);
        } else {
            $G = get_file("sql_file", true);
        }
        if (is_string($G)) {
            if (function_exists('memory_get_usage')) {
                @ini_set("memory_limit", max(ini_bytes("memory_limit"), 2 * strlen($G) + memory_get_usage() + 8e6));
            }
            if ($G != "" && strlen($G) < 1e6) {
                $rg = $G . (preg_match("~;[ \t\r\n]*\$~", $G) ? "" : ";");
                if (!$zd || reset(end($zd)) != $rg) {
                    restart_session();
                    $zd[] = [$rg, time()];
                    set_session("queries", $_d);
                    stop_session();
                }
            }
            $th = "(?:\\s|/\\*[\s\S]*?\\*/|(?:#|-- )[^\n]*\n?|--\r?\n)";
            $Wb = ";";
            $D = 0;
            $uc = true;
            $h = connect();
            if (is_object($h) && DB != "") {
                $h->select_db(DB);
            }
            $ub = 0;
            $zc = [];
            $Nf = '[\'"' . ($x == "sql" ? '`#' : ($x == "sqlite" ? '`[' : ($x == "mssql" ? '[' : ''))) . ']|/\*|-- |$' . ($x == "pgsql" ? '|\$[^$]*\$' : '');
            $mi = microtime(true);
            parse_str($_COOKIE["adminer_export"], $ya);
            $lc = $b->dumpFormat();
            unset($lc["sql"]);
            while ($G != "") {
                if (!$D && preg_match("~^$th*+DELIMITER\\s+(\\S+)~i", $G, $B)) {
                    $Wb = $B[1];
                    $G = substr($G, strlen($B[0]));
                } else {
                    preg_match('(' . preg_quote($Wb) . "\\s*|$Nf)", $G, $B, PREG_OFFSET_CAPTURE, $D);
                    list($hd, $bg) = $B[0];
                    if (!$hd && $jd && !feof($jd)) {
                        $G .= fread($jd, 1e5);
                    } else {
                        if (!$hd && rtrim($G) == "") {
                            break;
                        }
                        $D = $bg + strlen($hd);
                        if ($hd && rtrim($hd) != $Wb) {
                            while (preg_match('(' . ($hd == '/*' ? '\*/' : ($hd == '[' ? ']' : (preg_match('~^-- |^#~', $hd) ? "\n" : preg_quote($hd) . "|\\\\."))) . '|$)s', $G, $B, PREG_OFFSET_CAPTURE, $D)) {
                                $Vg = $B[0][0];
                                if (!$Vg && $jd && !feof($jd)) {
                                    $G .= fread($jd, 1e5);
                                } else {
                                    $D = $B[0][1] + strlen($Vg);
                                    if ($Vg[0] != "\\") {
                                        break;
                                    }
                                }
                            }
                        } else {
                            $uc = false;
                            $rg = substr($G, 0, $bg);
                            $ub++;
                            $jg = "<pre id='sql-$ub'><code class='jush-$x'>" . $b->sqlCommandQuery($rg) . "</code></pre>\n";
                            if ($x == "sqlite" && preg_match("~^$th*+ATTACH\\b~i", $rg, $B)) {
                                echo $jg, "<p class='error'>" . lang(146) . "\n";
                                $zc[] = " <a href='#sql-$ub'>$ub</a>";
                                if ($_POST["error_stops"]) {
                                    break;
                                }
                            } else {
                                if (!$_POST["only_errors"]) {
                                    echo $jg;
                                    ob_flush();
                                    flush();
                                }
                                $_h = microtime(true);
                                if ($g->multi_query($rg) && is_object($h) && preg_match("~^$th*+USE\\b~i", $rg)) {
                                    $h->query($rg);
                                }
                                do {
                                    $H = $g->store_result();
                                    if ($g->error) {
                                        echo($_POST["only_errors"] ? $jg : ""), "<p class='error'>" . lang(147) . ($g->errno ? " ($g->errno)" : "") . ": " . error() . "\n";
                                        $zc[] = " <a href='#sql-$ub'>$ub</a>";
                                        if ($_POST["error_stops"]) {
                                            break
                                        2;
                                        }
                                    } else {
                                        $ai = " <span class='time'>(" . format_time($_h) . ")</span>" . (strlen($rg) < 1000 ? " <a href='" . h(ME) . "sql=" . urlencode(trim($rg)) . "'>" . lang(10) . "</a>" : "");
                                        $_a = $g->affected_rows;
                                        $Zi = ($_POST["only_errors"] ? "" : $m->warnings());
                                        $aj = "warnings-$ub";
                                        if ($Zi) {
                                            $ai .= ", <a href='#$aj'>" . lang(47) . "</a>" . script("qsl('a').onclick = partial(toggle, '$aj');", "");
                                        }
                                        $Gc = null;
                                        $Hc = "explain-$ub";
                                        if (is_object($H)) {
                                            $z = $_POST["limit"];
                                            $_f = select($H, $h, [], $z);
                                            if (!$_POST["only_errors"]) {
                                                echo "<form action='' method='post'>\n";
                                                $df = $H->num_rows;
                                                echo "<p>" . ($df ? ($z && $df > $z ? lang(148, $z) : "") . lang(149, $df) : ""), $ai;
                                                if ($h && preg_match("~^($th|\\()*+SELECT\\b~i", $rg) && ($Gc = explain($h, $rg))) {
                                                    echo ", <a href='#$Hc'>Explain</a>" . script("qsl('a').onclick = partial(toggle, '$Hc');", "");
                                                }
                                                $t = "export-$ub";
                                                echo ", <a href='#$t'>" . lang(74) . "</a>" . script("qsl('a').onclick = partial(toggle, '$t');", "") . "<span id='$t' class='hidden'>: " . html_select("output", $b->dumpOutput(), $ya["output"]) . " " . html_select("format", $lc, $ya["format"]) . "<input type='hidden' name='query' value='" . h($rg) . "'>" . " <input type='submit' name='export' value='" . lang(74) . "'><input type='hidden' name='token' value='$ki'></span>\n" . "</form>\n";
                                            }
                                        } else {
                                            if (preg_match("~^$th*+(CREATE|DROP|ALTER)$th++(DATABASE|SCHEMA)\\b~i", $rg)) {
                                                restart_session();
                                                set_session("dbs", null);
                                                stop_session();
                                            }
                                            if (!$_POST["only_errors"]) {
                                                echo "<p class='message' title='" . h($g->info) . "'>" . lang(150, $_a) . "$ai\n";
                                            }
                                        }
                                        echo($Zi ? "<div id='$aj' class='hidden'>\n$Zi</div>\n" : "");
                                        if ($Gc) {
                                            echo "<div id='$Hc' class='hidden'>\n";
                                            select($Gc, $h, $_f);
                                            echo "</div>\n";
                                        }
                                    }
                                    $_h = microtime(true);
                                } while ($g->next_result());
                            }
                            $G = substr($G, $D);
                            $D = 0;
                        }
                    }
                }
            }
            if ($uc) {
                echo "<p class='message'>" . lang(151) . "\n";
            } elseif ($_POST["only_errors"]) {
                echo "<p class='message'>" . lang(152, $ub - count($zc)), " <span class='time'>(" . format_time($mi) . ")</span>\n";
            } elseif ($zc && $ub > 1) {
                echo "<p class='error'>" . lang(147) . ": " . implode("", $zc) . "\n";
            }
        } else {
            echo "<p class='error'>" . upload_error($G) . "\n";
        }
    }
    echo '
<form action="" method="post" enctype="multipart/form-data" id="form">
';
    $Dc = "<input type='submit' value='" . lang(153) . "' title='Ctrl+Enter'>";
    if (!isset($_GET["import"])) {
        $rg = $_GET["sql"];
        if ($_POST) {
            $rg = $_POST["query"];
        } elseif ($_GET["history"] == "all") {
            $rg = $zd;
        } elseif ($_GET["history"] != "") {
            $rg = $zd[$_GET["history"]][0];
        }
        echo "<p>";
        textarea("query", $rg, 20);
        echo($_POST ? "" : script("qs('textarea').focus();")), "<p>$Dc\n", lang(154) . ": <input type='number' name='limit' class='size' value='" . h($_POST ? $_POST["limit"] : $_GET["limit"]) . "'>\n";
    } else {
        echo "<fieldset><legend>" . lang(155) . "</legend><div>";
        $sd = (extension_loaded("zlib") ? "[.gz]" : "");
        echo(ini_bool("file_uploads") ? "SQL$sd (&lt; " . ini_get("upload_max_filesize") . "B): <input type='file' name='sql_file[]' multiple>\n$Dc" : lang(156)), "</div></fieldset>\n", "<fieldset><legend>" . lang(157) . "</legend><div>", lang(158, "<code>" . h($b->importServerPath()) . "$sd</code>"), ' <input type="submit" name="webfile" value="' . lang(159) . '">', "</div></fieldset>\n", "<p>";
    }
    echo
        checkbox("error_stops", 1, ($_POST ? $_POST["error_stops"] : isset($_GET["import"])), lang(160)) . "\n", checkbox("only_errors", 1, ($_POST ? $_POST["only_errors"] : isset($_GET["import"])), lang(161)) . "\n", "<input type='hidden' name='token' value='$ki'>\n";
    if (!isset($_GET["import"]) && $zd) {
        print_fieldset("history", lang(162), $_GET["history"] != "");
        for ($X = end($zd); $X; $X = prev($zd)) {
            $y = key($zd);
            list($rg, $ai, $pc) = $X;
            echo '<a href="' . h(ME . "sql=&history=$y") . '">' . lang(10) . "</a>" . " <span class='time' title='" . @date('Y-m-d', $ai) . "'>" . @date("H:i:s", $ai) . "</span>" . " <code class='jush-$x'>" . shorten_utf8(ltrim(str_replace("\n", " ", str_replace("\r", "", preg_replace('~^(#|-- ).*~m', '', $rg)))), 80, "</code>") . ($pc ? " <span class='time'>($pc)</span>" : "") . "<br>\n";
        }
        echo "<input type='submit' name='clear' value='" . lang(163) . "'>\n", "<a href='" . h(ME . "sql=&history=all") . "'>" . lang(164) . "</a>\n", "</div></fieldset>\n";
    }
    echo '</form>
';
} elseif (isset($_GET["edit"])) {
    $a = $_GET["edit"];
    $p = fields($a);
    $Z = (isset($_GET["select"]) ? ($_POST["check"] && count($_POST["check"]) == 1 ? where_check($_POST["check"][0], $p) : "") : where($_GET, $p));
    $Gi = (isset($_GET["select"]) ? $_POST["edit"] : $Z);
    foreach ($p
             as $C => $o) {
        if (!isset($o["privileges"][$Gi ? "update" : "insert"]) || $b->fieldName($o) == "") {
            unset($p[$C]);
        }
    }
    if ($_POST && !$n && !isset($_GET["select"])) {
        $A = $_POST["referer"];
        if ($_POST["insert"]) {
            $A = ($Gi ? null : $_SERVER["REQUEST_URI"]);
        } elseif (!preg_match('~^.+&select=.+$~', $A)) {
            $A = ME . "select=" . urlencode($a);
        }
        $w = indexes($a);
        $Bi = unique_array($_GET["where"], $w);
        $ug = "\nWHERE $Z";
        if (isset($_POST["delete"])) {
            queries_redirect($A, lang(165), $m->delete($a, $ug, !$Bi));
        } else {
            $O = [];
            foreach ($p
                     as $C => $o) {
                $X = process_input($o);
                if ($X !== false && $X !== null) {
                    $O[idf_escape($C)] = $X;
                }
            }
            if ($Gi) {
                if (!$O) {
                    redirect($A);
                }
                queries_redirect($A, lang(166), $m->update($a, $O, $ug, !$Bi));
                if (is_ajax()) {
                    page_headers();
                    page_messages($n);
                    exit;
                }
            } else {
                $H = $m->insert($a, $O);
                $oe = ($H ? last_id() : 0);
                queries_redirect($A, lang(167, ($oe ? " $oe" : "")), $H);
            }
        }
    }
    $J = null;
    if ($_POST["save"]) {
        $J = (array)$_POST["fields"];
    } elseif ($Z) {
        $L = [];
        foreach ($p
                 as $C => $o) {
            if (isset($o["privileges"]["select"])) {
                $Ha = convert_field($o);
                if ($_POST["clone"] && $o["auto_increment"]) {
                    $Ha = "''";
                }
                if ($x == "sql" && preg_match("~enum|set~", $o["type"])) {
                    $Ha = "1*" . idf_escape($C);
                }
                $L[] = ($Ha ? "$Ha AS " : "") . idf_escape($C);
            }
        }
        $J = [];
        if (!support("table")) {
            $L = ["*"];
        }
        if ($L) {
            $H = $m->select($a, $L, [$Z], $L, [], (isset($_GET["select"]) ? 2 : 1));
            if (!$H) {
                $n = error();
            } else {
                $J = $H->fetch_assoc();
                if (!$J) {
                    $J = false;
                }
            }
            if (isset($_GET["select"]) && (!$J || $H->fetch_assoc())) {
                $J = null;
            }
        }
    }
    if (!support("table") && !$p) {
        if (!$Z) {
            $H = $m->select($a, ["*"], $Z, ["*"]);
            $J = ($H ? $H->fetch_assoc() : false);
            if (!$J) {
                $J = [$m->primary => ""];
            }
        }
        if ($J) {
            foreach ($J
                     as $y => $X) {
                if (!$Z) {
                    $J[$y] = null;
                }
                $p[$y] = ["field" => $y, "null" => ($y != $m->primary), "auto_increment" => ($y == $m->primary)];
            }
        }
    }
    edit_form($a, $p, $J, $Gi);
} elseif (isset($_GET["create"])) {
    $a = $_GET["create"];
    $Pf = [];
    foreach (['HASH', 'LINEAR HASH', 'KEY', 'LINEAR KEY', 'RANGE', 'LIST'] as $y) {
        $Pf[$y] = $y;
    }
    $Bg = referencable_primary($a);
    $fd = [];
    foreach ($Bg
             as $Lh => $o) {
        $fd[str_replace("`", "``", $Lh) . "`" . str_replace("`", "``", $o["field"])] = $Lh;
    }
    $Cf = [];
    $S = [];
    if ($a != "") {
        $Cf = fields($a);
        $S = table_status($a);
        if (!$S) {
            $n = lang(9);
        }
    }
    $J = $_POST;
    $J["fields"] = (array)$J["fields"];
    if ($J["auto_increment_col"]) {
        $J["fields"][$J["auto_increment_col"]]["auto_increment"] = true;
    }
    if ($_POST && !process_fields($J["fields"]) && !$n) {
        if ($_POST["drop"]) {
            queries_redirect(substr(ME, 0, -1), lang(168), drop_tables([$a]));
        } else {
            $p = [];
            $Ea = [];
            $Li = false;
            $dd = [];
            $Bf = reset($Cf);
            $Ba = " FIRST";
            foreach ($J["fields"] as $y => $o) {
                $q = $fd[$o["type"]];
                $xi = ($q !== null ? $Bg[$q] : $o);
                if ($o["field"] != "") {
                    if (!$o["has_default"]) {
                        $o["default"] = null;
                    }
                    if ($y == $J["auto_increment_col"]) {
                        $o["auto_increment"] = true;
                    }
                    $og = process_field($o, $xi);
                    $Ea[] = [$o["orig"], $og, $Ba];
                    if ($og != process_field($Bf, $Bf)) {
                        $p[] = [$o["orig"], $og, $Ba];
                        if ($o["orig"] != "" || $Ba) {
                            $Li = true;
                        }
                    }
                    if ($q !== null) {
                        $dd[idf_escape($o["field"])] = ($a != "" && $x != "sqlite" ? "ADD" : " ") . format_foreign_key(['table' => $fd[$o["type"]], 'source' => [$o["field"]], 'target' => [$xi["field"]], 'on_delete' => $o["on_delete"],]);
                    }
                    $Ba = " AFTER " . idf_escape($o["field"]);
                } elseif ($o["orig"] != "") {
                    $Li = true;
                    $p[] = [$o["orig"]];
                }
                if ($o["orig"] != "") {
                    $Bf = next($Cf);
                    if (!$Bf) {
                        $Ba = "";
                    }
                }
            }
            $Rf = "";
            if ($Pf[$J["partition_by"]]) {
                $Sf = [];
                if ($J["partition_by"] == 'RANGE' || $J["partition_by"] == 'LIST') {
                    foreach (array_filter($J["partition_names"]) as $y => $X) {
                        $Y = $J["partition_values"][$y];
                        $Sf[] = "\n  PARTITION " . idf_escape($X) . " VALUES " . ($J["partition_by"] == 'RANGE' ? "LESS THAN" : "IN") . ($Y != "" ? " ($Y)" : " MAXVALUE");
                    }
                }
                $Rf .= "\nPARTITION BY $J[partition_by]($J[partition])" . ($Sf ? " (" . implode(",", $Sf) . "\n)" : ($J["partitions"] ? " PARTITIONS " . (+$J["partitions"]) : ""));
            } elseif (support("partitioning") && preg_match("~partitioned~", $S["Create_options"])) {
                $Rf .= "\nREMOVE PARTITIONING";
            }
            $Ke = lang(169);
            if ($a == "") {
                cookie("adminer_engine", $J["Engine"]);
                $Ke = lang(170);
            }
            $C = trim($J["name"]);
            queries_redirect(ME . (support("table") ? "table=" : "select=") . urlencode($C), $Ke, alter_table($a, $C, ($x == "sqlite" && ($Li || $dd) ? $Ea : $p), $dd, ($J["Comment"] != $S["Comment"] ? $J["Comment"] : null), ($J["Engine"] && $J["Engine"] != $S["Engine"] ? $J["Engine"] : ""), ($J["Collation"] && $J["Collation"] != $S["Collation"] ? $J["Collation"] : ""), ($J["Auto_increment"] != "" ? number($J["Auto_increment"]) : ""), $Rf));
        }
    }
    page_header(($a != "" ? lang(45) : lang(75)), $n, ["table" => $a], h($a));
    if (!$_POST) {
        $J = ["Engine" => $_COOKIE["adminer_engine"], "fields" => [["field" => "", "type" => (isset($zi["int"]) ? "int" : (isset($zi["integer"]) ? "integer" : "")), "on_update" => ""]], "partition_names" => [""],];
        if ($a != "") {
            $J = $S;
            $J["name"] = $a;
            $J["fields"] = [];
            if (!$_GET["auto_increment"]) {
                $J["Auto_increment"] = "";
            }
            foreach ($Cf
                     as $o) {
                $o["has_default"] = isset($o["default"]);
                $J["fields"][] = $o;
            }
            if (support("partitioning")) {
                $kd = "FROM information_schema.PARTITIONS WHERE TABLE_SCHEMA = " . q(DB) . " AND TABLE_NAME = " . q($a);
                $H = $g->query("SELECT PARTITION_METHOD, PARTITION_ORDINAL_POSITION, PARTITION_EXPRESSION $kd ORDER BY PARTITION_ORDINAL_POSITION DESC LIMIT 1");
                list($J["partition_by"], $J["partitions"], $J["partition"]) = $H->fetch_row();
                $Sf = get_key_vals("SELECT PARTITION_NAME, PARTITION_DESCRIPTION $kd AND PARTITION_NAME != '' ORDER BY PARTITION_ORDINAL_POSITION");
                $Sf[""] = "";
                $J["partition_names"] = array_keys($Sf);
                $J["partition_values"] = array_values($Sf);
            }
        }
    }
    $qb = collations();
    $wc = engines();
    foreach ($wc
             as $vc) {
        if (!strcasecmp($vc, $J["Engine"])) {
            $J["Engine"] = $vc;
            break;
        }
    }
    echo '
<form action="" method="post" id="form">
<p>
';
    if (support("columns") || $a == "") {
        echo
        lang(171), ': <input name="name" maxlength="64" value="', h($J["name"]), '" autocapitalize="off">
';
        if ($a == "" && !$_POST) {
            echo
        script("focus(qs('#form')['name']);");
        }
        echo($wc ? "<select name='Engine'>" . optionlist(["" => "(" . lang(172) . ")"] + $wc, $J["Engine"]) . "</select>" . on_help("getTarget(event).value", 1) . script("qsl('select').onchange = helpClose;") : ""), ' ', ($qb && !preg_match("~sqlite|mssql~", $x) ? html_select("Collation", ["" => "(" . lang(100) . ")"] + $qb, $J["Collation"]) : ""), ' <input type="submit" value="', lang(14), '">
';
    }
    echo '
';
    if (support("columns")) {
        echo '<table cellspacing="0" id="edit-fields" class="nowrap">
';
        $wb = ($_POST ? $_POST["comments"] : $J["Comment"] != "");
        if (!$_POST && !$wb) {
            foreach ($J["fields"] as $o) {
                if ($o["comment"] != "") {
                    $wb = true;
                    break;
                }
            }
        }
        edit_fields($J["fields"], $qb, "TABLE", $fd, $wb);
        echo '</table>
<p>
', lang(52), ': <input type="number" name="Auto_increment" size="6" value="', h($J["Auto_increment"]), '">
', checkbox("defaults", 1, !$_POST || $_POST["defaults"], lang(173), "columnShow(this.checked, 5)", "jsonly"), ($_POST ? "" : script("editingHideDefaults();")), (support("comment") ? "<label><input type='checkbox' name='comments' value='1' class='jsonly'" . ($wb ? " checked" : "") . ">" . lang(51) . "</label>" . script("qsl('input').onclick = partial(editingCommentsClick, true);") . ' <input name="Comment" value="' . h($J["Comment"]) . '" maxlength="' . (min_version(5.5) ? 2048 : 60) . '"' . ($wb ? '' : ' class="hidden"') . '>' : ''), '<p>
<input type="submit" value="', lang(14), '">
';
    }
    echo '
';
    if ($a != "") {
        echo '<input type="submit" name="drop" value="', lang(126), '">', confirm(lang(174, $a));
    }
    if (support("partitioning")) {
        $Qf = preg_match('~RANGE|LIST~', $J["partition_by"]);
        print_fieldset("partition", lang(175), $J["partition_by"]);
        echo '<p>
', "<select name='partition_by'>" . optionlist(["" => ""] + $Pf, $J["partition_by"]) . "</select>" . on_help("getTarget(event).value.replace(/./, 'PARTITION BY \$&')", 1) . script("qsl('select').onchange = partitionByChange;"), '(<input name="partition" value="', h($J["partition"]), '">)
', lang(176), ': <input type="number" name="partitions" class="size', ($Qf || !$J["partition_by"] ? " hidden" : ""), '" value="', h($J["partitions"]), '">
<table cellspacing="0" id="partition-table"', ($Qf ? "" : " class='hidden'"), '>
<thead><tr><th>', lang(177), '<th>', lang(178), '</thead>
';
        foreach ($J["partition_names"] as $y => $X) {
            echo '<tr>', '<td><input name="partition_names[]" value="' . h($X) . '" autocapitalize="off">', ($y == count($J["partition_names"]) - 1 ? script("qsl('input').oninput = partitionNameChange;") : ''), '<td><input name="partition_values[]" value="' . h($J["partition_values"][$y]) . '">';
        }
        echo '</table>
</div></fieldset>
';
    }
    echo '<input type="hidden" name="token" value="', $ki, '">
</form>
', script("qs('#form')['defaults'].onclick();" . (support("comment") ? " editingCommentsClick.call(qs('#form')['comments']);" : ""));
} elseif (isset($_GET["indexes"])) {
    $a = $_GET["indexes"];
    $Id = ["PRIMARY", "UNIQUE", "INDEX"];
    $S = table_status($a, true);
    if (preg_match('~MyISAM|M?aria' . (min_version(5.6, '10.0.5') ? '|InnoDB' : '') . '~i', $S["Engine"])) {
        $Id[] = "FULLTEXT";
    }
    if (preg_match('~MyISAM|M?aria' . (min_version(5.7, '10.2.2') ? '|InnoDB' : '') . '~i', $S["Engine"])) {
        $Id[] = "SPATIAL";
    }
    $w = indexes($a);
    $hg = [];
    if ($x == "mongo") {
        $hg = $w["_id_"];
        unset($Id[0]);
        unset($w["_id_"]);
    }
    $J = $_POST;
    if ($_POST && !$n && !$_POST["add"] && !$_POST["drop_col"]) {
        $c = [];
        foreach ($J["indexes"] as $v) {
            $C = $v["name"];
            if (in_array($v["type"], $Id)) {
                $e = [];
                $ue = [];
                $Yb = [];
                $O = [];
                ksort($v["columns"]);
                foreach ($v["columns"] as $y => $d) {
                    if ($d != "") {
                        $te = $v["lengths"][$y];
                        $Xb = $v["descs"][$y];
                        $O[] = idf_escape($d) . ($te ? "(" . (+$te) . ")" : "") . ($Xb ? " DESC" : "");
                        $e[] = $d;
                        $ue[] = ($te ? $te : null);
                        $Yb[] = $Xb;
                    }
                }
                if ($e) {
                    $Ec = $w[$C];
                    if ($Ec) {
                        ksort($Ec["columns"]);
                        ksort($Ec["lengths"]);
                        ksort($Ec["descs"]);
                        if ($v["type"] == $Ec["type"] && array_values($Ec["columns"]) === $e && (!$Ec["lengths"] || array_values($Ec["lengths"]) === $ue) && array_values($Ec["descs"]) === $Yb) {
                            unset($w[$C]);
                            continue;
                        }
                    }
                    $c[] = [$v["type"], $C, $O];
                }
            }
        }
        foreach ($w
                 as $C => $Ec) {
            $c[] = [$Ec["type"], $C, "DROP"];
        }
        if (!$c) {
            redirect(ME . "table=" . urlencode($a));
        }
        queries_redirect(ME . "table=" . urlencode($a), lang(179), alter_indexes($a, $c));
    }
    page_header(lang(131), $n, ["table" => $a], h($a));
    $p = array_keys(fields($a));
    if ($_POST["add"]) {
        foreach ($J["indexes"] as $y => $v) {
            if ($v["columns"][count($v["columns"])] != "") {
                $J["indexes"][$y]["columns"][] = "";
            }
        }
        $v = end($J["indexes"]);
        if ($v["type"] || array_filter($v["columns"], 'strlen')) {
            $J["indexes"][] = ["columns" => [1 => ""]];
        }
    }
    if (!$J) {
        foreach ($w
                 as $y => $v) {
            $w[$y]["name"] = $y;
            $w[$y]["columns"][] = "";
        }
        $w[] = ["columns" => [1 => ""]];
        $J["indexes"] = $w;
    }
    echo '
<form action="" method="post">
<table cellspacing="0" class="nowrap">
<thead><tr>
<th id="label-type">', lang(180), '<th><input type="submit" class="wayoff">', lang(181), '<th id="label-name">', lang(182), '<th><noscript>', "<input type='image' class='icon' name='add[0]' src='" . h(preg_replace("~\\?.*~", "", ME) . "?file=plus.gif&version=4.6.3") . "' alt='+' title='" . lang(107) . "'>", '</noscript>
</thead>
';
    if ($hg) {
        echo "<tr><td>PRIMARY<td>";
        foreach ($hg["columns"] as $y => $d) {
            echo
            select_input(" disabled", $p, $d), "<label><input disabled type='checkbox'>" . lang(60) . "</label> ";
        }
        echo "<td><td>\n";
    }
    $be = 1;
    foreach ($J["indexes"] as $v) {
        if (!$_POST["drop_col"] || $be != key($_POST["drop_col"])) {
            echo "<tr><td>" . html_select("indexes[$be][type]", [-1 => ""] + $Id, $v["type"], ($be == count($J["indexes"]) ? "indexesAddRow.call(this);" : 1), "label-type"), "<td>";
            ksort($v["columns"]);
            $s = 1;
            foreach ($v["columns"] as $y => $d) {
                echo "<span>" . select_input(" name='indexes[$be][columns][$s]' title='" . lang(49) . "'", ($p ? array_combine($p, $p) : $p), $d, "partial(" . ($s == count($v["columns"]) ? "indexesAddColumn" : "indexesChangeColumn") . ", '" . js_escape($x == "sql" ? "" : $_GET["indexes"] . "_") . "')"), ($x == "sql" || $x == "mssql" ? "<input type='number' name='indexes[$be][lengths][$s]' class='size' value='" . h($v["lengths"][$y]) . "' title='" . lang(105) . "'>" : ""), ($x != "sql" ? checkbox("indexes[$be][descs][$s]", 1, $v["descs"][$y], lang(60)) : ""), " </span>";
                $s++;
            }
            echo "<td><input name='indexes[$be][name]' value='" . h($v["name"]) . "' autocapitalize='off' aria-labelledby='label-name'>\n", "<td><input type='image' class='icon' name='drop_col[$be]' src='" . h(preg_replace("~\\?.*~", "", ME) . "?file=cross.gif&version=4.6.3") . "' alt='x' title='" . lang(110) . "'>" . script("qsl('input').onclick = partial(editingRemoveRow, 'indexes\$1[type]');");
        }
        $be++;
    }
    echo '</table>
<p>
<input type="submit" value="', lang(14), '">
<input type="hidden" name="token" value="', $ki, '">
</form>
';
} elseif (isset($_GET["database"])) {
    $J = $_POST;
    if ($_POST && !$n && !isset($_POST["add_x"])) {
        $C = trim($J["name"]);
        if ($_POST["drop"]) {
            $_GET["db"] = "";
            queries_redirect(remove_from_uri("db|database"), lang(183), drop_databases([DB]));
        } elseif (DB !== $C) {
            if (DB != "") {
                $_GET["db"] = $C;
                queries_redirect(preg_replace('~\bdb=[^&]*&~', '', ME) . "db=" . urlencode($C), lang(184), rename_database($C, $J["collation"]));
            } else {
                $k = explode("\n", str_replace("\r", "", $C));
                $Fh = true;
                $ne = "";
                foreach ($k
                         as $l) {
                    if (count($k) == 1 || $l != "") {
                        if (!create_database($l, $J["collation"])) {
                            $Fh = false;
                        }
                        $ne = $l;
                    }
                }
                restart_session();
                set_session("dbs", null);
                queries_redirect(ME . "db=" . urlencode($ne), lang(185), $Fh);
            }
        } else {
            if (!$J["collation"]) {
                redirect(substr(ME, 0, -1));
            }
            query_redirect("ALTER DATABASE " . idf_escape($C) . (preg_match('~^[a-z0-9_]+$~i', $J["collation"]) ? " COLLATE $J[collation]" : ""), substr(ME, 0, -1), lang(186));
        }
    }
    page_header(DB != "" ? lang(68) : lang(114), $n, [], h(DB));
    $qb = collations();
    $C = DB;
    if ($_POST) {
        $C = $J["name"];
    } elseif (DB != "") {
        $J["collation"] = db_collation(DB, $qb);
    } elseif ($x == "sql") {
        foreach (get_vals("SHOW GRANTS") as $md) {
            if (preg_match('~ ON (`(([^\\\\`]|``|\\\\.)*)%`\.\*)?~', $md, $B) && $B[1]) {
                $C = stripcslashes(idf_unescape("`$B[2]`"));
                break;
            }
        }
    }
    echo '
<form action="" method="post">
<p>
', ($_POST["add_x"] || strpos($C, "\n") ? '<textarea id="name" name="name" rows="10" cols="40">' . h($C) . '</textarea><br>' : '<input name="name" id="name" value="' . h($C) . '" maxlength="64" autocapitalize="off">') . "\n" . ($qb ? html_select("collation", ["" => "(" . lang(100) . ")"] + $qb, $J["collation"]) . doc_link(['sql' => "charset-charsets.html", 'mariadb' => "supported-character-sets-and-collations/", 'mssql' => "ms187963.aspx",]) : ""), script("focus(qs('#name'));"), '<input type="submit" value="', lang(14), '">
';
    if (DB != "") {
        echo "<input type='submit' name='drop' value='" . lang(126) . "'>" . confirm(lang(174, DB)) . "\n";
    } elseif (!$_POST["add_x"] && $_GET["db"] == "") {
        echo "<input type='image' class='icon' name='add' src='" . h(preg_replace("~\\?.*~", "", ME) . "?file=plus.gif&version=4.6.3") . "' alt='+' title='" . lang(107) . "'>\n";
    }
    echo '<input type="hidden" name="token" value="', $ki, '">
</form>
';
} elseif (isset($_GET["scheme"])) {
    $J = $_POST;
    if ($_POST && !$n) {
        $_ = preg_replace('~ns=[^&]*&~', '', ME) . "ns=";
        if ($_POST["drop"]) {
            query_redirect("DROP SCHEMA " . idf_escape($_GET["ns"]), $_, lang(187));
        } else {
            $C = trim($J["name"]);
            $_ .= urlencode($C);
            if ($_GET["ns"] == "") {
                query_redirect("CREATE SCHEMA " . idf_escape($C), $_, lang(188));
            } elseif ($_GET["ns"] != $C) {
                query_redirect("ALTER SCHEMA " . idf_escape($_GET["ns"]) . " RENAME TO " . idf_escape($C), $_, lang(189));
            } else {
                redirect($_);
            }
        }
    }
    page_header($_GET["ns"] != "" ? lang(69) : lang(70), $n);
    if (!$J) {
        $J["name"] = $_GET["ns"];
    }
    echo '
<form action="" method="post">
<p><input name="name" id="name" value="', h($J["name"]), '" autocapitalize="off">
', script("focus(qs('#name'));"), '<input type="submit" value="', lang(14), '">
';
    if ($_GET["ns"] != "") {
        echo "<input type='submit' name='drop' value='" . lang(126) . "'>" . confirm(lang(174, $_GET["ns"])) . "\n";
    }
    echo '<input type="hidden" name="token" value="', $ki, '">
</form>
';
} elseif (isset($_GET["call"])) {
    $da = ($_GET["name"] ? $_GET["name"] : $_GET["call"]);
    page_header(lang(190) . ": " . h($da), $n);
    $Rg = routine($_GET["call"], (isset($_GET["callf"]) ? "FUNCTION" : "PROCEDURE"));
    $Gd = [];
    $Gf = [];
    foreach ($Rg["fields"] as $s => $o) {
        if (substr($o["inout"], -3) == "OUT") {
            $Gf[$s] = "@" . idf_escape($o["field"]) . " AS " . idf_escape($o["field"]);
        }
        if (!$o["inout"] || substr($o["inout"], 0, 2) == "IN") {
            $Gd[] = $s;
        }
    }
    if (!$n && $_POST) {
        $ab = [];
        foreach ($Rg["fields"] as $y => $o) {
            if (in_array($y, $Gd)) {
                $X = process_input($o);
                if ($X === false) {
                    $X = "''";
                }
                if (isset($Gf[$y])) {
                    $g->query("SET @" . idf_escape($o["field"]) . " = $X");
                }
            }
            $ab[] = (isset($Gf[$y]) ? "@" . idf_escape($o["field"]) : $X);
        }
        $G = (isset($_GET["callf"]) ? "SELECT" : "CALL") . " " . table($da) . "(" . implode(", ", $ab) . ")";
        $_h = microtime(true);
        $H = $g->multi_query($G);
        $_a = $g->affected_rows;
        echo $b->selectQuery($G, $_h, !$H);
        if (!$H) {
            echo "<p class='error'>" . error() . "\n";
        } else {
            $h = connect();
            if (is_object($h)) {
                $h->select_db(DB);
            }
            do {
                $H = $g->store_result();
                if (is_object($H)) {
                    select($H, $h);
                } else {
                    echo "<p class='message'>" . lang(191, $_a) . "\n";
                }
            } while ($g->next_result());
            if ($Gf) {
                select($g->query("SELECT " . implode(", ", $Gf)));
            }
        }
    }
    echo '
<form action="" method="post">
';
    if ($Gd) {
        echo "<table cellspacing='0'>\n";
        foreach ($Gd
                 as $y) {
            $o = $Rg["fields"][$y];
            $C = $o["field"];
            echo "<tr><th>" . $b->fieldName($o);
            $Y = $_POST["fields"][$C];
            if ($Y != "") {
                if ($o["type"] == "enum") {
                    $Y = +$Y;
                }
                if ($o["type"] == "set") {
                    $Y = array_sum($Y);
                }
            }
            input($o, $Y, (string)$_POST["function"][$C]);
            echo "\n";
        }
        echo "</table>\n";
    }
    echo '<p>
<input type="submit" value="', lang(190), '">
<input type="hidden" name="token" value="', $ki, '">
</form>
';
} elseif (isset($_GET["foreign"])) {
    $a = $_GET["foreign"];
    $C = $_GET["name"];
    $J = $_POST;
    if ($_POST && !$n && !$_POST["add"] && !$_POST["change"] && !$_POST["change-js"]) {
        $Ke = ($_POST["drop"] ? lang(192) : ($C != "" ? lang(193) : lang(194)));
        $A = ME . "table=" . urlencode($a);
        if (!$_POST["drop"]) {
            $J["source"] = array_filter($J["source"], 'strlen');
            ksort($J["source"]);
            $Th = [];
            foreach ($J["source"] as $y => $X) {
                $Th[$y] = $J["target"][$y];
            }
            $J["target"] = $Th;
        }
        if ($x == "sqlite") {
            queries_redirect($A, $Ke, recreate_table($a, $a, [], [], [" $C" => ($_POST["drop"] ? "" : " " . format_foreign_key($J))]));
        } else {
            $c = "ALTER TABLE " . table($a);
            $gc = "\nDROP " . ($x == "sql" ? "FOREIGN KEY " : "CONSTRAINT ") . idf_escape($C);
            if ($_POST["drop"]) {
                query_redirect($c . $gc, $A, $Ke);
            } else {
                query_redirect($c . ($C != "" ? "$gc," : "") . "\nADD" . format_foreign_key($J), $A, $Ke);
                $n = lang(195) . "<br>$n";
            }
        }
    }
    page_header(lang(196), $n, ["table" => $a], h($a));
    if ($_POST) {
        ksort($J["source"]);
        if ($_POST["add"]) {
            $J["source"][] = "";
        } elseif ($_POST["change"] || $_POST["change-js"]) {
            $J["target"] = [];
        }
    } elseif ($C != "") {
        $fd = foreign_keys($a);
        $J = $fd[$C];
        $J["source"][] = "";
    } else {
        $J["table"] = $a;
        $J["source"] = [""];
    }
    $sh = array_keys(fields($a));
    $Th = ($a === $J["table"] ? $sh : array_keys(fields($J["table"])));
    $Ag = array_keys(array_filter(table_status('', true), 'fk_support'));
    echo '
<form action="" method="post">
<p>
';
    if ($J["db"] == "" && $J["ns"] == "") {
        echo
        lang(197), ':
', html_select("table", $Ag, $J["table"], "this.form['change-js'].value = '1'; this.form.submit();"), '<input type="hidden" name="change-js" value="">
<noscript><p><input type="submit" name="change" value="', lang(198), '"></noscript>
<table cellspacing="0">
<thead><tr><th id="label-source">', lang(133), '<th id="label-target">', lang(134), '</thead>
';
        $be = 0;
        foreach ($J["source"] as $y => $X) {
            echo "<tr>", "<td>" . html_select("source[" . (+$y) . "]", [-1 => ""] + $sh, $X, ($be == count($J["source"]) - 1 ? "foreignAddRow.call(this);" : 1), "label-source"), "<td>" . html_select("target[" . (+$y) . "]", $Th, $J["target"][$y], 1, "label-target");
            $be++;
        }
        echo '</table>
<p>
', lang(102), ': ', html_select("on_delete", [-1 => ""] + explode("|", $nf), $J["on_delete"]), ' ', lang(101), ': ', html_select("on_update", [-1 => ""] + explode("|", $nf), $J["on_update"]), doc_link(['sql' => "innodb-foreign-key-constraints.html", 'mariadb' => "foreign-keys/", 'pgsql' => "sql-createtable.html#SQL-CREATETABLE-REFERENCES", 'mssql' => "ms174979.aspx", 'oracle' => "clauses002.htm#sthref2903",]), '<p>
<input type="submit" value="', lang(14), '">
<noscript><p><input type="submit" name="add" value="', lang(199), '"></noscript>
';
    }
    if ($C != "") {
        echo '<input type="submit" name="drop" value="', lang(126), '">', confirm(lang(174, $C));
    }
    echo '<input type="hidden" name="token" value="', $ki, '">
</form>
';
} elseif (isset($_GET["view"])) {
    $a = $_GET["view"];
    $J = $_POST;
    $Df = "VIEW";
    if ($x == "pgsql" && $a != "") {
        $P = table_status($a);
        $Df = strtoupper($P["Engine"]);
    }
    if ($_POST && !$n) {
        $C = trim($J["name"]);
        $Ha = " AS\n$J[select]";
        $A = ME . "table=" . urlencode($C);
        $Ke = lang(200);
        $U = ($_POST["materialized"] ? "MATERIALIZED VIEW" : "VIEW");
        if (!$_POST["drop"] && $a == $C && $x != "sqlite" && $U == "VIEW" && $Df == "VIEW") {
            query_redirect(($x == "mssql" ? "ALTER" : "CREATE OR REPLACE") . " VIEW " . table($C) . $Ha, $A, $Ke);
        } else {
            $Vh = $C . "_adminer_" . uniqid();
            drop_create("DROP $Df " . table($a), "CREATE $U " . table($C) . $Ha, "DROP $U " . table($C), "CREATE $U " . table($Vh) . $Ha, "DROP $U " . table($Vh), ($_POST["drop"] ? substr(ME, 0, -1) : $A), lang(201), $Ke, lang(202), $a, $C);
        }
    }
    if (!$_POST && $a != "") {
        $J = view($a);
        $J["name"] = $a;
        $J["materialized"] = ($Df != "VIEW");
        if (!$n) {
            $n = error();
        }
    }
    page_header(($a != "" ? lang(44) : lang(203)), $n, ["table" => $a], h($a));
    echo '
<form action="" method="post">
<p>', lang(182), ': <input name="name" value="', h($J["name"]), '" maxlength="64" autocapitalize="off">
', (support("materializedview") ? " " . checkbox("materialized", 1, $J["materialized"], lang(128)) : ""), '<p>';
    textarea("select", $J["select"]);
    echo '<p>
<input type="submit" value="', lang(14), '">
';
    if ($a != "") {
        echo '<input type="submit" name="drop" value="', lang(126), '">', confirm(lang(174, $a));
    }
    echo '<input type="hidden" name="token" value="', $ki, '">
</form>
';
} elseif (isset($_GET["event"])) {
    $aa = $_GET["event"];
    $Td = ["YEAR", "QUARTER", "MONTH", "DAY", "HOUR", "MINUTE", "WEEK", "SECOND", "YEAR_MONTH", "DAY_HOUR", "DAY_MINUTE", "DAY_SECOND", "HOUR_MINUTE", "HOUR_SECOND", "MINUTE_SECOND"];
    $Bh = ["ENABLED" => "ENABLE", "DISABLED" => "DISABLE", "SLAVESIDE_DISABLED" => "DISABLE ON SLAVE"];
    $J = $_POST;
    if ($_POST && !$n) {
        if ($_POST["drop"]) {
            query_redirect("DROP EVENT " . idf_escape($aa), substr(ME, 0, -1), lang(204));
        } elseif (in_array($J["INTERVAL_FIELD"], $Td) && isset($Bh[$J["STATUS"]])) {
            $Wg = "\nON SCHEDULE " . ($J["INTERVAL_VALUE"] ? "EVERY " . q($J["INTERVAL_VALUE"]) . " $J[INTERVAL_FIELD]" . ($J["STARTS"] ? " STARTS " . q($J["STARTS"]) : "") . ($J["ENDS"] ? " ENDS " . q($J["ENDS"]) : "") : "AT " . q($J["STARTS"])) . " ON COMPLETION" . ($J["ON_COMPLETION"] ? "" : " NOT") . " PRESERVE";
            queries_redirect(substr(ME, 0, -1), ($aa != "" ? lang(205) : lang(206)), queries(($aa != "" ? "ALTER EVENT " . idf_escape($aa) . $Wg . ($aa != $J["EVENT_NAME"] ? "\nRENAME TO " . idf_escape($J["EVENT_NAME"]) : "") : "CREATE EVENT " . idf_escape($J["EVENT_NAME"]) . $Wg) . "\n" . $Bh[$J["STATUS"]] . " COMMENT " . q($J["EVENT_COMMENT"]) . rtrim(" DO\n$J[EVENT_DEFINITION]", ";") . ";"));
        }
    }
    page_header(($aa != "" ? lang(207) . ": " . h($aa) : lang(208)), $n);
    if (!$J && $aa != "") {
        $K = get_rows("SELECT * FROM information_schema.EVENTS WHERE EVENT_SCHEMA = " . q(DB) . " AND EVENT_NAME = " . q($aa));
        $J = reset($K);
    }
    echo '
<form action="" method="post">
<table cellspacing="0">
<tr><th>', lang(182), '<td><input name="EVENT_NAME" value="', h($J["EVENT_NAME"]), '" maxlength="64" autocapitalize="off">
<tr><th title="datetime">', lang(209), '<td><input name="STARTS" value="', h("$J[EXECUTE_AT]$J[STARTS]"), '">
<tr><th title="datetime">', lang(210), '<td><input name="ENDS" value="', h($J["ENDS"]), '">
<tr><th>', lang(211), '<td><input type="number" name="INTERVAL_VALUE" value="', h($J["INTERVAL_VALUE"]), '" class="size"> ', html_select("INTERVAL_FIELD", $Td, $J["INTERVAL_FIELD"]), '<tr><th>', lang(117), '<td>', html_select("STATUS", $Bh, $J["STATUS"]), '<tr><th>', lang(51), '<td><input name="EVENT_COMMENT" value="', h($J["EVENT_COMMENT"]), '" maxlength="64">
<tr><th><td>', checkbox("ON_COMPLETION", "PRESERVE", $J["ON_COMPLETION"] == "PRESERVE", lang(212)), '</table>
<p>';
    textarea("EVENT_DEFINITION", $J["EVENT_DEFINITION"]);
    echo '<p>
<input type="submit" value="', lang(14), '">
';
    if ($aa != "") {
        echo '<input type="submit" name="drop" value="', lang(126), '">', confirm(lang(174, $aa));
    }
    echo '<input type="hidden" name="token" value="', $ki, '">
</form>
';
} elseif (isset($_GET["procedure"])) {
    $da = ($_GET["name"] ? $_GET["name"] : $_GET["procedure"]);
    $Rg = (isset($_GET["function"]) ? "FUNCTION" : "PROCEDURE");
    $J = $_POST;
    $J["fields"] = (array)$J["fields"];
    if ($_POST && !process_fields($J["fields"]) && !$n) {
        $Af = routine($_GET["procedure"], $Rg);
        $Vh = "$J[name]_adminer_" . uniqid();
        drop_create("DROP $Rg " . routine_id($da, $Af), create_routine($Rg, $J), "DROP $Rg " . routine_id($J["name"], $J), create_routine($Rg, ["name" => $Vh] + $J), "DROP $Rg " . routine_id($Vh, $J), substr(ME, 0, -1), lang(213), lang(214), lang(215), $da, $J["name"]);
    }
    page_header(($da != "" ? (isset($_GET["function"]) ? lang(216) : lang(217)) . ": " . h($da) : (isset($_GET["function"]) ? lang(218) : lang(219))), $n);
    if (!$_POST && $da != "") {
        $J = routine($_GET["procedure"], $Rg);
        $J["name"] = $da;
    }
    $qb = get_vals("SHOW CHARACTER SET");
    sort($qb);
    $Sg = routine_languages();
    echo '
<form action="" method="post" id="form">
<p>', lang(182), ': <input name="name" value="', h($J["name"]), '" maxlength="64" autocapitalize="off">
', ($Sg ? lang(19) . ": " . html_select("language", $Sg, $J["language"]) . "\n" : ""), '<input type="submit" value="', lang(14), '">
<table cellspacing="0" class="nowrap">
';
    edit_fields($J["fields"], $qb, $Rg);
    if (isset($_GET["function"])) {
        echo "<tr><td>" . lang(220);
        edit_type("returns", $J["returns"], $qb, [], ($x == "pgsql" ? ["void", "trigger"] : []));
    }
    echo '</table>
<p>';
    textarea("definition", $J["definition"]);
    echo '<p>
<input type="submit" value="', lang(14), '">
';
    if ($da != "") {
        echo '<input type="submit" name="drop" value="', lang(126), '">', confirm(lang(174, $da));
    }
    echo '<input type="hidden" name="token" value="', $ki, '">
</form>
';
} elseif (isset($_GET["sequence"])) {
    $fa = $_GET["sequence"];
    $J = $_POST;
    if ($_POST && !$n) {
        $_ = substr(ME, 0, -1);
        $C = trim($J["name"]);
        if ($_POST["drop"]) {
            query_redirect("DROP SEQUENCE " . idf_escape($fa), $_, lang(221));
        } elseif ($fa == "") {
            query_redirect("CREATE SEQUENCE " . idf_escape($C), $_, lang(222));
        } elseif ($fa != $C) {
            query_redirect("ALTER SEQUENCE " . idf_escape($fa) . " RENAME TO " . idf_escape($C), $_, lang(223));
        } else {
            redirect($_);
        }
    }
    page_header($fa != "" ? lang(224) . ": " . h($fa) : lang(225), $n);
    if (!$J) {
        $J["name"] = $fa;
    }
    echo '
<form action="" method="post">
<p><input name="name" value="', h($J["name"]), '" autocapitalize="off">
<input type="submit" value="', lang(14), '">
';
    if ($fa != "") {
        echo "<input type='submit' name='drop' value='" . lang(126) . "'>" . confirm(lang(174, $fa)) . "\n";
    }
    echo '<input type="hidden" name="token" value="', $ki, '">
</form>
';
} elseif (isset($_GET["type"])) {
    $ga = $_GET["type"];
    $J = $_POST;
    if ($_POST && !$n) {
        $_ = substr(ME, 0, -1);
        if ($_POST["drop"]) {
            query_redirect("DROP TYPE " . idf_escape($ga), $_, lang(226));
        } else {
            query_redirect("CREATE TYPE " . idf_escape(trim($J["name"])) . " $J[as]", $_, lang(227));
        }
    }
    page_header($ga != "" ? lang(228) . ": " . h($ga) : lang(229), $n);
    if (!$J) {
        $J["as"] = "AS ";
    }
    echo '
<form action="" method="post">
<p>
';
    if ($ga != "") {
        echo "<input type='submit' name='drop' value='" . lang(126) . "'>" . confirm(lang(174, $ga)) . "\n";
    } else {
        echo "<input name='name' value='" . h($J['name']) . "' autocapitalize='off'>\n";
        textarea("as", $J["as"]);
        echo "<p><input type='submit' value='" . lang(14) . "'>\n";
    }
    echo '<input type="hidden" name="token" value="', $ki, '">
</form>
';
} elseif (isset($_GET["trigger"])) {
    $a = $_GET["trigger"];
    $C = $_GET["name"];
    $vi = trigger_options();
    $J = (array)trigger($C) + ["Trigger" => $a . "_bi"];
    if ($_POST) {
        if (!$n && in_array($_POST["Timing"], $vi["Timing"]) && in_array($_POST["Event"], $vi["Event"]) && in_array($_POST["Type"], $vi["Type"])) {
            $mf = " ON " . table($a);
            $gc = "DROP TRIGGER " . idf_escape($C) . ($x == "pgsql" ? $mf : "");
            $A = ME . "table=" . urlencode($a);
            if ($_POST["drop"]) {
                query_redirect($gc, $A, lang(230));
            } else {
                if ($C != "") {
                    queries($gc);
                }
                queries_redirect($A, ($C != "" ? lang(231) : lang(232)), queries(create_trigger($mf, $_POST)));
                if ($C != "") {
                    queries(create_trigger($mf, $J + ["Type" => reset($vi["Type"])]));
                }
            }
        }
        $J = $_POST;
    }
    page_header(($C != "" ? lang(233) . ": " . h($C) : lang(234)), $n, ["table" => $a]);
    echo '
<form action="" method="post" id="form">
<table cellspacing="0">
<tr><th>', lang(235), '<td>', html_select("Timing", $vi["Timing"], $J["Timing"], "triggerChange(/^" . preg_quote($a, "/") . "_[ba][iud]$/, '" . js_escape($a) . "', this.form);"), '<tr><th>', lang(236), '<td>', html_select("Event", $vi["Event"], $J["Event"], "this.form['Timing'].onchange();"), (in_array("UPDATE OF", $vi["Event"]) ? " <input name='Of' value='" . h($J["Of"]) . "' class='hidden'>" : ""), '<tr><th>', lang(50), '<td>', html_select("Type", $vi["Type"], $J["Type"]), '</table>
<p>', lang(182), ': <input name="Trigger" value="', h($J["Trigger"]), '" maxlength="64" autocapitalize="off">
', script("qs('#form')['Timing'].onchange();"), '<p>';
    textarea("Statement", $J["Statement"]);
    echo '<p>
<input type="submit" value="', lang(14), '">
';
    if ($C != "") {
        echo '<input type="submit" name="drop" value="', lang(126), '">', confirm(lang(174, $C));
    }
    echo '<input type="hidden" name="token" value="', $ki, '">
</form>
';
} elseif (isset($_GET["user"])) {
    $ha = $_GET["user"];
    $mg = ["" => ["All privileges" => ""]];
    foreach (get_rows("SHOW PRIVILEGES") as $J) {
        foreach (explode(",", ($J["Privilege"] == "Grant option" ? "" : $J["Context"])) as $Bb) {
            $mg[$Bb][$J["Privilege"]] = $J["Comment"];
        }
    }
    $mg["Server Admin"] += $mg["File access on server"];
    $mg["Databases"]["Create routine"] = $mg["Procedures"]["Create routine"];
    unset($mg["Procedures"]["Create routine"]);
    $mg["Columns"] = [];
    foreach (["Select", "Insert", "Update", "References"] as $X) {
        $mg["Columns"][$X] = $mg["Tables"][$X];
    }
    unset($mg["Server Admin"]["Usage"]);
    foreach ($mg["Tables"] as $y => $X) {
        unset($mg["Databases"][$y]);
    }
    $Xe = [];
    if ($_POST) {
        foreach ($_POST["objects"] as $y => $X) {
            $Xe[$X] = (array)$Xe[$X] + (array)$_POST["grants"][$y];
        }
    }
    $nd = [];
    $kf = "";
    if (isset($_GET["host"]) && ($H = $g->query("SHOW GRANTS FOR " . q($ha) . "@" . q($_GET["host"])))) {
        while ($J = $H->fetch_row()) {
            if (preg_match('~GRANT (.*) ON (.*) TO ~', $J[0], $B) && preg_match_all('~ *([^(,]*[^ ,(])( *\([^)]+\))?~', $B[1], $Ce, PREG_SET_ORDER)) {
                foreach ($Ce
                         as $X) {
                    if ($X[1] != "USAGE") {
                        $nd["$B[2]$X[2]"][$X[1]] = true;
                    }
                    if (preg_match('~ WITH GRANT OPTION~', $J[0])) {
                        $nd["$B[2]$X[2]"]["GRANT OPTION"] = true;
                    }
                }
            }
            if (preg_match("~ IDENTIFIED BY PASSWORD '([^']+)~", $J[0], $B)) {
                $kf = $B[1];
            }
        }
    }
    if ($_POST && !$n) {
        $lf = (isset($_GET["host"]) ? q($ha) . "@" . q($_GET["host"]) : "''");
        if ($_POST["drop"]) {
            query_redirect("DROP USER $lf", ME . "privileges=", lang(237));
        } else {
            $Ze = q($_POST["user"]) . "@" . q($_POST["host"]);
            $Uf = $_POST["pass"];
            if ($Uf != '' && !$_POST["hashed"]) {
                $Uf = $g->result("SELECT PASSWORD(" . q($Uf) . ")");
                $n = !$Uf;
            }
            $Gb = false;
            if (!$n) {
                if ($lf != $Ze) {
                    $Gb = queries((min_version(5) ? "CREATE USER" : "GRANT USAGE ON *.* TO") . " $Ze IDENTIFIED BY PASSWORD " . q($Uf));
                    $n = !$Gb;
                } elseif ($Uf != $kf) {
                    queries("SET PASSWORD FOR $Ze = " . q($Uf));
                }
            }
            if (!$n) {
                $Og = [];
                foreach ($Xe
                         as $ff => $md) {
                    if (isset($_GET["grant"])) {
                        $md = array_filter($md);
                    }
                    $md = array_keys($md);
                    if (isset($_GET["grant"])) {
                        $Og = array_diff(array_keys(array_filter($Xe[$ff], 'strlen')), $md);
                    } elseif ($lf == $Ze) {
                        $if = array_keys((array)$nd[$ff]);
                        $Og = array_diff($if, $md);
                        $md = array_diff($md, $if);
                        unset($nd[$ff]);
                    }
                    if (preg_match('~^(.+)\s*(\(.*\))?$~U', $ff, $B) && (!grant("REVOKE", $Og, $B[2], " ON $B[1] FROM $Ze") || !grant("GRANT", $md, $B[2], " ON $B[1] TO $Ze"))) {
                        $n = true;
                        break;
                    }
                }
            }
            if (!$n && isset($_GET["host"])) {
                if ($lf != $Ze) {
                    queries("DROP USER $lf");
                } elseif (!isset($_GET["grant"])) {
                    foreach ($nd
                             as $ff => $Og) {
                        if (preg_match('~^(.+)(\(.*\))?$~U', $ff, $B)) {
                            grant("REVOKE", array_keys($Og), $B[2], " ON $B[1] FROM $Ze");
                        }
                    }
                }
            }
            queries_redirect(ME . "privileges=", (isset($_GET["host"]) ? lang(238) : lang(239)), !$n);
            if ($Gb) {
                $g->query("DROP USER $Ze");
            }
        }
    }
    page_header((isset($_GET["host"]) ? lang(36) . ": " . h("$ha@$_GET[host]") : lang(145)), $n, ["privileges" => ['', lang(72)]]);
    if ($_POST) {
        $J = $_POST;
        $nd = $Xe;
    } else {
        $J = $_GET + ["host" => $g->result("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', -1)")];
        $J["pass"] = $kf;
        if ($kf != "") {
            $J["hashed"] = true;
        }
        $nd[(DB == "" || $nd ? "" : idf_escape(addcslashes(DB, "%_\\"))) . ".*"] = [];
    }
    echo '<form action="" method="post">
<table cellspacing="0">
<tr><th>', lang(35), '<td><input name="host" maxlength="60" value="', h($J["host"]), '" autocapitalize="off">
<tr><th>', lang(36), '<td><input name="user" maxlength="16" value="', h($J["user"]), '" autocapitalize="off">
<tr><th>', lang(37), '<td><input name="pass" id="pass" value="', h($J["pass"]), '" autocomplete="new-password">
';
    if (!$J["hashed"]) {
        echo
    script("typePassword(qs('#pass'));");
    }
    echo
    checkbox("hashed", 1, $J["hashed"], lang(240), "typePassword(this.form['pass'], this.checked);"), '</table>

';
    echo "<table cellspacing='0'>\n", "<thead><tr><th colspan='2'>" . lang(72) . doc_link(['sql' => "grant.html#priv_level"]);
    $s = 0;
    foreach ($nd
             as $ff => $md) {
        echo '<th>' . ($ff != "*.*" ? "<input name='objects[$s]' value='" . h($ff) . "' size='10' autocapitalize='off'>" : "<input type='hidden' name='objects[$s]' value='*.*' size='10'>*.*");
        $s++;
    }
    echo "</thead>\n";
    foreach (["" => "", "Server Admin" => lang(35), "Databases" => lang(38), "Tables" => lang(130), "Columns" => lang(49), "Procedures" => lang(241),] as $Bb => $Xb) {
        foreach ((array)$mg[$Bb] as $lg => $vb) {
            echo "<tr" . odd() . "><td" . ($Xb ? ">$Xb<td" : " colspan='2'") . ' lang="en" title="' . h($vb) . '">' . h($lg);
            $s = 0;
            foreach ($nd
                     as $ff => $md) {
                $C = "'grants[$s][" . h(strtoupper($lg)) . "]'";
                $Y = $md[strtoupper($lg)];
                if ($Bb == "Server Admin" && $ff != (isset($nd["*.*"]) ? "*.*" : ".*")) {
                    echo "<td>";
                } elseif (isset($_GET["grant"])) {
                    echo "<td><select name=$C><option><option value='1'" . ($Y ? " selected" : "") . ">" . lang(242) . "<option value='0'" . ($Y == "0" ? " selected" : "") . ">" . lang(243) . "</select>";
                } else {
                    echo "<td align='center'><label class='block'>", "<input type='checkbox' name=$C value='1'" . ($Y ? " checked" : "") . ($lg == "All privileges" ? " id='grants-$s-all'>" : ">" . ($lg == "Grant option" ? "" : script("qsl('input').onclick = function () { if (this.checked) formUncheck('grants-$s-all'); };"))), "</label>";
                }
                $s++;
            }
        }
    }
    echo "</table>\n", '<p>
<input type="submit" value="', lang(14), '">
';
    if (isset($_GET["host"])) {
        echo '<input type="submit" name="drop" value="', lang(126), '">', confirm(lang(174, "$ha@$_GET[host]"));
    }
    echo '<input type="hidden" name="token" value="', $ki, '">
</form>
';
} elseif (isset($_GET["processlist"])) {
    if (support("kill") && $_POST && !$n) {
        $ie = 0;
        foreach ((array)$_POST["kill"] as $X) {
            if (kill_process($X)) {
                $ie++;
            }
        }
        queries_redirect(ME . "processlist=", lang(244, $ie), $ie || !$_POST["kill"]);
    }
    page_header(lang(115), $n);
    echo '
<form action="" method="post">
<table cellspacing="0" class="nowrap checkable">
', script("mixin(qsl('table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true)});");
    $s = -1;
    foreach (process_list() as $s => $J) {
        if (!$s) {
            echo "<thead><tr lang='en'>" . (support("kill") ? "<th>" : "");
            foreach ($J
                     as $y => $X) {
                echo "<th>$y" . doc_link(['sql' => "show-processlist.html#processlist_" . strtolower($y), 'pgsql' => "monitoring-stats.html#PG-STAT-ACTIVITY-VIEW", 'oracle' => "../b14237/dynviews_2088.htm",]);
            }
            echo "</thead>\n";
        }
        echo "<tr" . odd() . ">" . (support("kill") ? "<td>" . checkbox("kill[]", $J[$x == "sql" ? "Id" : "pid"], 0) : "");
        foreach ($J
                 as $y => $X) {
            echo "<td>" . (($x == "sql" && $y == "Info" && preg_match("~Query|Killed~", $J["Command"]) && $X != "") || ($x == "pgsql" && $y == "current_query" && $X != "<IDLE>") || ($x == "oracle" && $y == "sql_text" && $X != "") ? "<code class='jush-$x'>" . shorten_utf8($X, 100, "</code>") . ' <a href="' . h(ME . ($J["db"] != "" ? "db=" . urlencode($J["db"]) . "&" : "") . "sql=" . urlencode($X)) . '">' . lang(245) . '</a>' : h($X));
        }
        echo "\n";
    }
    echo '</table>
<p>
';
    if (support("kill")) {
        echo($s + 1) . "/" . lang(246, max_connections()), "<p><input type='submit' value='" . lang(247) . "'>\n";
    }
    echo '<input type="hidden" name="token" value="', $ki, '">
</form>
', script("tableCheck();");
} elseif (isset($_GET["select"])) {
    $a = $_GET["select"];
    $S = table_status1($a);
    $w = indexes($a);
    $p = fields($a);
    $fd = column_foreign_keys($a);
    $hf = $S["Oid"];
    parse_str($_COOKIE["adminer_import"], $za);
    $Pg = [];
    $e = [];
    $Zh = null;
    foreach ($p
             as $y => $o) {
        $C = $b->fieldName($o);
        if (isset($o["privileges"]["select"]) && $C != "") {
            $e[$y] = html_entity_decode(strip_tags($C), ENT_QUOTES);
            if (is_shortable($o)) {
                $Zh = $b->selectLengthProcess();
            }
        }
        $Pg += $o["privileges"];
    }
    list($L, $od) = $b->selectColumnsProcess($e, $w);
    $Xd = count($od) < count($L);
    $Z = $b->selectSearchProcess($p, $w);
    $xf = $b->selectOrderProcess($p, $w);
    $z = $b->selectLimitProcess();
    if ($_GET["val"] && is_ajax()) {
        header("Content-Type: text/plain; charset=utf-8");
        foreach ($_GET["val"] as $Ci => $J) {
            $Ha = convert_field($p[key($J)]);
            $L = [$Ha ? $Ha : idf_escape(key($J))];
            $Z[] = where_check($Ci, $p);
            $I = $m->select($a, $L, $Z, $L);
            if ($I) {
                echo
            reset($I->fetch_row());
            }
        }
        exit;
    }
    $hg = $Ei = null;
    foreach ($w
             as $v) {
        if ($v["type"] == "PRIMARY") {
            $hg = array_flip($v["columns"]);
            $Ei = ($L ? $hg : []);
            foreach ($Ei
                     as $y => $X) {
                if (in_array(idf_escape($y), $L)) {
                    unset($Ei[$y]);
                }
            }
            break;
        }
    }
    if ($hf && !$hg) {
        $hg = $Ei = [$hf => 0];
        $w[] = ["type" => "PRIMARY", "columns" => [$hf]];
    }
    if ($_POST && !$n) {
        $fj = $Z;
        if (!$_POST["all"] && is_array($_POST["check"])) {
            $gb = [];
            foreach ($_POST["check"] as $db) {
                $gb[] = where_check($db, $p);
            }
            $fj[] = "((" . implode(") OR (", $gb) . "))";
        }
        $fj = ($fj ? "\nWHERE " . implode(" AND ", $fj) : "");
        if ($_POST["export"]) {
            cookie("adminer_import", "output=" . urlencode($_POST["output"]) . "&format=" . urlencode($_POST["format"]));
            dump_headers($a);
            $b->dumpTable($a, "");
            $kd = ($L ? implode(", ", $L) : "*") . convert_fields($e, $p, $L) . "\nFROM " . table($a);
            $qd = ($od && $Xd ? "\nGROUP BY " . implode(", ", $od) : "") . ($xf ? "\nORDER BY " . implode(", ", $xf) : "");
            if (!is_array($_POST["check"]) || $hg) {
                $G = "SELECT $kd$fj$qd";
            } else {
                $Ai = [];
                foreach ($_POST["check"] as $X) {
                    $Ai[] = "(SELECT" . limit($kd, "\nWHERE " . ($Z ? implode(" AND ", $Z) . " AND " : "") . where_check($X, $p) . $qd, 1) . ")";
                }
                $G = implode(" UNION ALL ", $Ai);
            }
            $b->dumpData($a, "table", $G);
            exit;
        }
        if (!$b->selectEmailProcess($Z, $fd)) {
            if ($_POST["save"] || $_POST["delete"]) {
                $H = true;
                $_a = 0;
                $O = [];
                if (!$_POST["delete"]) {
                    foreach ($e
                             as $C => $X) {
                        $X = process_input($p[$C]);
                        if ($X !== null && ($_POST["clone"] || $X !== false)) {
                            $O[idf_escape($C)] = ($X !== false ? $X : idf_escape($C));
                        }
                    }
                }
                if ($_POST["delete"] || $O) {
                    if ($_POST["clone"]) {
                        $G = "INTO " . table($a) . " (" . implode(", ", array_keys($O)) . ")\nSELECT " . implode(", ", $O) . "\nFROM " . table($a);
                    }
                    if ($_POST["all"] || ($hg && is_array($_POST["check"])) || $Xd) {
                        $H = ($_POST["delete"] ? $m->delete($a, $fj) : ($_POST["clone"] ? queries("INSERT $G$fj") : $m->update($a, $O, $fj)));
                        $_a = $g->affected_rows;
                    } else {
                        foreach ((array)$_POST["check"] as $X) {
                            $bj = "\nWHERE " . ($Z ? implode(" AND ", $Z) . " AND " : "") . where_check($X, $p);
                            $H = ($_POST["delete"] ? $m->delete($a, $bj, 1) : ($_POST["clone"] ? queries("INSERT" . limit1($a, $G, $bj)) : $m->update($a, $O, $bj, 1)));
                            if (!$H) {
                                break;
                            }
                            $_a += $g->affected_rows;
                        }
                    }
                }
                $Ke = lang(248, $_a);
                if ($_POST["clone"] && $H && $_a == 1) {
                    $oe = last_id();
                    if ($oe) {
                        $Ke = lang(167, " $oe");
                    }
                }
                queries_redirect(remove_from_uri($_POST["all"] && $_POST["delete"] ? "page" : ""), $Ke, $H);
                if (!$_POST["delete"]) {
                    edit_form($a, $p, (array)$_POST["fields"], !$_POST["clone"]);
                    page_footer();
                    exit;
                }
            } elseif (!$_POST["import"]) {
                if (!$_POST["val"]) {
                    $n = lang(249);
                } else {
                    $H = true;
                    $_a = 0;
                    foreach ($_POST["val"] as $Ci => $J) {
                        $O = [];
                        foreach ($J
                                 as $y => $X) {
                            $y = bracket_escape($y, 1);
                            $O[idf_escape($y)] = (preg_match('~char|text~', $p[$y]["type"]) || $X != "" ? $b->processInput($p[$y], $X) : "NULL");
                        }
                        $H = $m->update($a, $O, " WHERE " . ($Z ? implode(" AND ", $Z) . " AND " : "") . where_check($Ci, $p), !$Xd && !$hg, " ");
                        if (!$H) {
                            break;
                        }
                        $_a += $g->affected_rows;
                    }
                    queries_redirect(remove_from_uri(), lang(248, $_a), $H);
                }
            } elseif (!is_string($Uc = get_file("csv_file", true))) {
                $n = upload_error($Uc);
            } elseif (!preg_match('~~u', $Uc)) {
                $n = lang(250);
            } else {
                cookie("adminer_import", "output=" . urlencode($za["output"]) . "&format=" . urlencode($_POST["separator"]));
                $H = true;
                $sb = array_keys($p);
                preg_match_all('~(?>"[^"]*"|[^"\r\n]+)+~', $Uc, $Ce);
                $_a = count($Ce[0]);
                $m->begin();
                $M = ($_POST["separator"] == "csv" ? "," : ($_POST["separator"] == "tsv" ? "\t" : ";"));
                $K = [];
                foreach ($Ce[0] as $y => $X) {
                    preg_match_all("~((?>\"[^\"]*\")+|[^$M]*)$M~", $X . $M, $De);
                    if (!$y && !array_diff($De[1], $sb)) {
                        $sb = $De[1];
                        $_a--;
                    } else {
                        $O = [];
                        foreach ($De[1] as $s => $nb) {
                            $O[idf_escape($sb[$s])] = ($nb == "" && $p[$sb[$s]]["null"] ? "NULL" : q(str_replace('""', '"', preg_replace('~^"|"$~', '', $nb))));
                        }
                        $K[] = $O;
                    }
                }
                $H = (!$K || $m->insertUpdate($a, $K, $hg));
                if ($H) {
                    $H = $m->commit();
                }
                queries_redirect(remove_from_uri("page"), lang(251, $_a), $H);
                $m->rollback();
            }
        }
    }
    $Lh = $b->tableName($S);
    if (is_ajax()) {
        page_headers();
        ob_start();
    } else {
        page_header(lang(54) . ": $Lh", $n);
    }
    $O = null;
    if (isset($Pg["insert"]) || !support("table")) {
        $O = "";
        foreach ((array)$_GET["where"] as $X) {
            if ($fd[$X["col"]] && count($fd[$X["col"]]) == 1 && ($X["op"] == "=" || (!$X["op"] && !preg_match('~[_%]~', $X["val"])))) {
                $O .= "&set" . urlencode("[" . bracket_escape($X["col"]) . "]") . "=" . urlencode($X["val"]);
            }
        }
    }
    $b->selectLinks($S, $O);
    if (!$e && support("table")) {
        echo "<p class='error'>" . lang(252) . ($p ? "." : ": " . error()) . "\n";
    } else {
        echo "<form action='' id='form'>\n", "<div style='display: none;'>";
        hidden_fields_get();
        echo(DB != "" ? '<input type="hidden" name="db" value="' . h(DB) . '">' . (isset($_GET["ns"]) ? '<input type="hidden" name="ns" value="' . h($_GET["ns"]) . '">' : "") : "");
        echo '<input type="hidden" name="select" value="' . h($a) . '">', "</div>\n";
        $b->selectColumnsPrint($L, $e);
        $b->selectSearchPrint($Z, $e, $w);
        $b->selectOrderPrint($xf, $e, $w);
        $b->selectLimitPrint($z);
        $b->selectLengthPrint($Zh);
        $b->selectActionPrint($w);
        echo "</form>\n";
        $E = $_GET["page"];
        if ($E == "last") {
            $id = $g->result(count_rows($a, $Z, $Xd, $od));
            $E = floor(max(0, $id - 1) / $z);
        }
        $bh = $L;
        $pd = $od;
        if (!$bh) {
            $bh[] = "*";
            $Cb = convert_fields($e, $p, $L);
            if ($Cb) {
                $bh[] = substr($Cb, 2);
            }
        }
        foreach ($L
                 as $y => $X) {
            $o = $p[idf_unescape($X)];
            if ($o && ($Ha = convert_field($o))) {
                $bh[$y] = "$Ha AS $X";
            }
        }
        if (!$Xd && $Ei) {
            foreach ($Ei
                     as $y => $X) {
                $bh[] = idf_escape($y);
                if ($pd) {
                    $pd[] = idf_escape($y);
                }
            }
        }
        $H = $m->select($a, $bh, $Z, $pd, $xf, $z, $E, true);
        if (!$H) {
            echo "<p class='error'>" . error() . "\n";
        } else {
            if ($x == "mssql" && $E) {
                $H->seek($z * $E);
            }
            $tc = [];
            echo "<form action='' method='post' enctype='multipart/form-data'>\n";
            $K = [];
            while ($J = $H->fetch_assoc()) {
                if ($E && $x == "oracle") {
                    unset($J["RNUM"]);
                }
                $K[] = $J;
            }
            if ($_GET["page"] != "last" && $z != "" && $od && $Xd && $x == "sql") {
                $id = $g->result(" SELECT FOUND_ROWS()");
            }
            if (!$K) {
                echo "<p class='message'>" . lang(12) . "\n";
            } else {
                $Qa = $b->backwardKeys($a, $Lh);
                echo "<table id='table' cellspacing='0' class='nowrap checkable'>", script("mixin(qs('#table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true), onkeydown: editingKeydown});"), "<thead><tr>" . (!$od && $L ? "" : "<td><input type='checkbox' id='all-page' class='jsonly'>" . script("qs('#all-page').onclick = partial(formCheck, /check/);", "") . " <a href='" . h($_GET["modify"] ? remove_from_uri("modify") : $_SERVER["REQUEST_URI"] . "&modify=1") . "'>" . lang(253) . "</a>");
                $We = [];
                $ld = [];
                reset($L);
                $wg = 1;
                foreach ($K[0] as $y => $X) {
                    if (!isset($Ei[$y])) {
                        $X = $_GET["columns"][key($L)];
                        $o = $p[$L ? ($X ? $X["col"] : current($L)) : $y];
                        $C = ($o ? $b->fieldName($o, $wg) : ($X["fun"] ? "*" : $y));
                        if ($C != "") {
                            $wg++;
                            $We[$y] = $C;
                            $d = idf_escape($y);
                            $Cd = remove_from_uri('(order|desc)[^=]*|page') . '&order%5B0%5D=' . urlencode($y);
                            $Xb = "&desc%5B0%5D=1";
                            echo "<th>" . script("mixin(qsl('th'), {onmouseover: partial(columnMouse), onmouseout: partial(columnMouse, ' hidden')});", ""), '<a href="' . h($Cd . ($xf[0] == $d || $xf[0] == $y || (!$xf && $Xd && $od[0] == $d) ? $Xb : '')) . '">';
                            echo
                                apply_sql_function($X["fun"], $C) . "</a>";
                            echo "<span class='column hidden'>", "<a href='" . h($Cd . $Xb) . "' title='" . lang(60) . "' class='text'> ↓</a>";
                            if (!$X["fun"]) {
                                echo '<a href="#fieldset-search" title="' . lang(57) . '" class="text jsonly"> =</a>', script("qsl('a').onclick = partial(selectSearch, '" . js_escape($y) . "');");
                            }
                            echo "</span>";
                        }
                        $ld[$y] = $X["fun"];
                        next($L);
                    }
                }
                $ue = [];
                if ($_GET["modify"]) {
                    foreach ($K
                             as $J) {
                        foreach ($J
                                 as $y => $X) {
                            $ue[$y] = max($ue[$y], min(40, strlen(utf8_decode($X))));
                        }
                    }
                }
                echo($Qa ? "<th>" . lang(254) : "") . "</thead>\n";
                if (is_ajax()) {
                    if ($z % 2 == 1 && $E % 2 == 1) {
                        odd();
                    }
                    ob_end_clean();
                }
                foreach ($b->rowDescriptions($K, $fd) as $Ve => $J) {
                    $Bi = unique_array($K[$Ve], $w);
                    if (!$Bi) {
                        $Bi = [];
                        foreach ($K[$Ve] as $y => $X) {
                            if (!preg_match('~^(COUNT\((\*|(DISTINCT )?`(?:[^`]|``)+`)\)|(AVG|GROUP_CONCAT|MAX|MIN|SUM)\(`(?:[^`]|``)+`\))$~', $y)) {
                                $Bi[$y] = $X;
                            }
                        }
                    }
                    $Ci = "";
                    foreach ($Bi
                             as $y => $X) {
                        if (($x == "sql" || $x == "pgsql") && preg_match('~char|text|enum|set~', $p[$y]["type"]) && strlen($X) > 64) {
                            $y = (strpos($y, '(') ? $y : idf_escape($y));
                            $y = "MD5(" . ($x != 'sql' || preg_match("~^utf8~", $p[$y]["collation"]) ? $y : "CONVERT($y USING " . charset($g) . ")") . ")";
                            $X = md5($X);
                        }
                        $Ci .= "&" . ($X !== null ? urlencode("where[" . bracket_escape($y) . "]") . "=" . urlencode($X) : "null%5B%5D=" . urlencode($y));
                    }
                    echo "<tr" . odd() . ">" . (!$od && $L ? "" : "<td>" . checkbox("check[]", substr($Ci, 1), in_array(substr($Ci, 1), (array)$_POST["check"])) . ($Xd || information_schema(DB) ? "" : " <a href='" . h(ME . "edit=" . urlencode($a) . $Ci) . "' class='edit'>" . lang(255) . "</a>"));
                    foreach ($J
                             as $y => $X) {
                        if (isset($We[$y])) {
                            $o = $p[$y];
                            $X = $m->value($X, $o);
                            if ($X != "" && (!isset($tc[$y]) || $tc[$y] != "")) {
                                $tc[$y] = (is_mail($X) ? $We[$y] : "");
                            }
                            $_ = "";
                            if (preg_match('~blob|bytea|raw|file~', $o["type"]) && $X != "") {
                                $_ = ME . 'download=' . urlencode($a) . '&field=' . urlencode($y) . $Ci;
                            }
                            if (!$_ && $X !== null) {
                                foreach ((array)$fd[$y] as $q) {
                                    if (count($fd[$y]) == 1 || end($q["source"]) == $y) {
                                        $_ = "";
                                        foreach ($q["source"] as $s => $sh) {
                                            $_ .= where_link($s, $q["target"][$s], $K[$Ve][$sh]);
                                        }
                                        $_ = ($q["db"] != "" ? preg_replace('~([?&]db=)[^&]+~', '\1' . urlencode($q["db"]), ME) : ME) . 'select=' . urlencode($q["table"]) . $_;
                                        if ($q["ns"]) {
                                            $_ = preg_replace('~([?&]ns=)[^&]+~', '\1' . urlencode($q["ns"]), $_);
                                        }
                                        if (count($q["source"]) == 1) {
                                            break;
                                        }
                                    }
                                }
                            }
                            if ($y == "COUNT(*)") {
                                $_ = ME . "select=" . urlencode($a);
                                $s = 0;
                                foreach ((array)$_GET["where"] as $W) {
                                    if (!array_key_exists($W["col"], $Bi)) {
                                        $_ .= where_link($s++, $W["col"], $W["val"], $W["op"]);
                                    }
                                }
                                foreach ($Bi
                                         as $ce => $W) {
                                    $_ .= where_link($s++, $ce, $W);
                                }
                            }
                            $X = select_value($X, $_, $o, $Zh);
                            $t = h("val[$Ci][" . bracket_escape($y) . "]");
                            $Y = $_POST["val"][$Ci][bracket_escape($y)];
                            $oc = !is_array($J[$y]) && is_utf8($X) && $K[$Ve][$y] == $J[$y] && !$ld[$y];
                            $Yh = preg_match('~text|lob~', $o["type"]);
                            if (($_GET["modify"] && $oc) || $Y !== null) {
                                $td = h($Y !== null ? $Y : $J[$y]);
                                echo "<td>" . ($Yh ? "<textarea name='$t' cols='30' rows='" . (substr_count($J[$y], "\n") + 1) . "'>$td</textarea>" : "<input name='$t' value='$td' size='$ue[$y]'>");
                            } else {
                                $ye = strpos($X, "<i>...</i>");
                                echo "<td id='$t' data-text='" . ($ye ? 2 : ($Yh ? 1 : 0)) . "'" . ($oc ? "" : " data-warning='" . h(lang(256)) . "'") . ">$X</td>";
                            }
                        }
                    }
                    if ($Qa) {
                        echo "<td>";
                    }
                    $b->backwardKeysPrint($Qa, $K[$Ve]);
                    echo "</tr>\n";
                }
                if (is_ajax()) {
                    exit;
                }
                echo "</table>\n";
            }
            if (!is_ajax()) {
                if ($K || $E) {
                    $Cc = true;
                    if ($_GET["page"] != "last") {
                        if ($z == "" || (count($K) < $z && ($K || !$E))) {
                            $id = ($E ? $E * $z : 0) + count($K);
                        } elseif ($x != "sql" || !$Xd) {
                            $id = ($Xd ? false : found_rows($S, $Z));
                            if ($id < max(1e4, 2 * ($E + 1) * $z)) {
                                $id = reset(slow_query(count_rows($a, $Z, $Xd, $od)));
                            } else {
                                $Cc = false;
                            }
                        }
                    }
                    $Jf = ($z != "" && ($id === false || $id > $z || $E));
                    if ($Jf) {
                        echo(($id === false ? count($K) + 1 : $id - $E * $z) > $z ? '<p><a href="' . h(remove_from_uri("page") . "&page=" . ($E + 1)) . '" class="loadmore">' . lang(257) . '</a>' . script("qsl('a').onclick = partial(selectLoadMore, " . (+$z) . ", '" . lang(258) . "...');", "") : ''), "\n";
                    }
                }
                echo "<div class='footer'><div>\n";
                if ($K || $E) {
                    if ($Jf) {
                        $Fe = ($id === false ? $E + (count($K) >= $z ? 2 : 1) : floor(($id - 1) / $z));
                        echo "<fieldset>";
                        if ($x != "simpledb") {
                            echo "<legend><a href='" . h(remove_from_uri("page")) . "'>" . lang(259) . "</a></legend>", script("qsl('a').onclick = function () { pageClick(this.href, +prompt('" . lang(259) . "', '" . ($E + 1) . "')); return false; };"), pagination(0, $E) . ($E > 5 ? " ..." : "");
                            for ($s = max(1, $E - 4); $s < min($Fe, $E + 5); $s++) {
                                echo
                            pagination($s, $E);
                            }
                            if ($Fe > 0) {
                                echo($E + 5 < $Fe ? " ..." : ""), ($Cc && $id !== false ? pagination($Fe, $E) : " <a href='" . h(remove_from_uri("page") . "&page=last") . "' title='~$Fe'>" . lang(260) . "</a>");
                            }
                        } else {
                            echo "<legend>" . lang(259) . "</legend>", pagination(0, $E) . ($E > 1 ? " ..." : ""), ($E ? pagination($E, $E) : ""), ($Fe > $E ? pagination($E + 1, $E) . ($Fe > $E + 1 ? " ..." : "") : "");
                        }
                        echo "</fieldset>\n";
                    }
                    echo "<fieldset>", "<legend>" . lang(261) . "</legend>";
                    $cc = ($Cc ? "" : "~ ") . $id;
                    echo
                        checkbox("all", 1, 0, ($id !== false ? ($Cc ? "" : "~ ") . lang(149, $id) : ""), "var checked = formChecked(this, /check/); selectCount('selected', this.checked ? '$cc' : checked); selectCount('selected2', this.checked || !checked ? '$cc' : checked);") . "\n", "</fieldset>\n";
                    if ($b->selectCommandPrint()) {
                        echo '<fieldset', ($_GET["modify"] ? '' : ' class="jsonly"'), '><legend>', lang(253), '</legend><div>
<input type="submit" value="', lang(14), '"', ($_GET["modify"] ? '' : ' title="' . lang(249) . '"'), '>
</div></fieldset>
<fieldset><legend>', lang(125), ' <span id="selected"></span></legend><div>
<input type="submit" name="edit" value="', lang(10), '">
<input type="submit" name="clone" value="', lang(245), '">
<input type="submit" name="delete" value="', lang(18), '">', confirm(), '</div></fieldset>
';
                    }
                    $gd = $b->dumpFormat();
                    foreach ((array)$_GET["columns"] as $d) {
                        if ($d["fun"]) {
                            unset($gd['sql']);
                            break;
                        }
                    }
                    if ($gd) {
                        print_fieldset("export", lang(74) . " <span id='selected2'></span>");
                        $Hf = $b->dumpOutput();
                        echo($Hf ? html_select("output", $Hf, $za["output"]) . " " : ""), html_select("format", $gd, $za["format"]), " <input type='submit' name='export' value='" . lang(74) . "'>\n", "</div></fieldset>\n";
                    }
                    $b->selectEmailPrint(array_filter($tc, 'strlen'), $e);
                }
                echo "</div></div>\n";
                if ($b->selectImportPrint()) {
                    echo "<div>", "<a href='#import'>" . lang(73) . "</a>", script("qsl('a').onclick = partial(toggle, 'import');", ""), "<span id='import' class='hidden'>: ", "<input type='file' name='csv_file'> ", html_select("separator", ["csv" => "CSV,", "csv;" => "CSV;", "tsv" => "TSV"], $za["format"], 1);
                    echo " <input type='submit' name='import' value='" . lang(73) . "'>", "</span>", "</div>";
                }
                echo "<input type='hidden' name='token' value='$ki'>\n", "</form>\n", (!$od && $L ? "" : script("tableCheck();"));
            }
        }
    }
    if (is_ajax()) {
        ob_end_clean();
        exit;
    }
} elseif (isset($_GET["variables"])) {
    $P = isset($_GET["status"]);
    page_header($P ? lang(117) : lang(116));
    $Si = ($P ? show_status() : show_variables());
    if (!$Si) {
        echo "<p class='message'>" . lang(12) . "\n";
    } else {
        echo "<table cellspacing='0'>\n";
        foreach ($Si
                 as $y => $X) {
            echo "<tr>", "<th><code class='jush-" . $x . ($P ? "status" : "set") . "'>" . h($y) . "</code>", "<td>" . h($X);
        }
        echo "</table>\n";
    }
} elseif (isset($_GET["script"])) {
    header("Content-Type: text/javascript; charset=utf-8");
    if ($_GET["script"] == "db") {
        $Ih = ["Data_length" => 0, "Index_length" => 0, "Data_free" => 0];
        foreach (table_status() as $C => $S) {
            json_row("Comment-$C", h($S["Comment"]));
            if (!is_view($S)) {
                foreach (["Engine", "Collation"] as $y) {
                    json_row("$y-$C", h($S[$y]));
                }
                foreach ($Ih + ["Auto_increment" => 0, "Rows" => 0] as $y => $X) {
                    if ($S[$y] != "") {
                        $X = format_number($S[$y]);
                        json_row("$y-$C", ($y == "Rows" && $X && $S["Engine"] == ($vh == "pgsql" ? "table" : "InnoDB") ? "~ $X" : $X));
                        if (isset($Ih[$y])) {
                            $Ih[$y] += ($S["Engine"] != "InnoDB" || $y != "Data_free" ? $S[$y] : 0);
                        }
                    } elseif (array_key_exists($y, $S)) {
                        json_row("$y-$C");
                    }
                }
            }
        }
        foreach ($Ih
                 as $y => $X) {
            json_row("sum-$y", format_number($X));
        }
        json_row("");
    } elseif ($_GET["script"] == "kill") {
        $g->query("KILL " . number($_POST["kill"]));
    } else {
        foreach (count_tables($b->databases()) as $l => $X) {
            json_row("tables-$l", $X);
            json_row("size-$l", db_size($l));
        }
        json_row("");
    }
    exit;
} else {
    $Rh = array_merge((array)$_POST["tables"], (array)$_POST["views"]);
    if ($Rh && !$n && !$_POST["search"]) {
        $H = true;
        $Ke = "";
        if ($x == "sql" && $_POST["tables"] && count($_POST["tables"]) > 1 && ($_POST["drop"] || $_POST["truncate"] || $_POST["copy"])) {
            queries("SET foreign_key_checks = 0");
        }
        if ($_POST["truncate"]) {
            if ($_POST["tables"]) {
                $H = truncate_tables($_POST["tables"]);
            }
            $Ke = lang(262);
        } elseif ($_POST["move"]) {
            $H = move_tables((array)$_POST["tables"], (array)$_POST["views"], $_POST["target"]);
            $Ke = lang(263);
        } elseif ($_POST["copy"]) {
            $H = copy_tables((array)$_POST["tables"], (array)$_POST["views"], $_POST["target"]);
            $Ke = lang(264);
        } elseif ($_POST["drop"]) {
            if ($_POST["views"]) {
                $H = drop_views($_POST["views"]);
            }
            if ($H && $_POST["tables"]) {
                $H = drop_tables($_POST["tables"]);
            }
            $Ke = lang(265);
        } elseif ($x != "sql") {
            $H = ($x == "sqlite" ? queries("VACUUM") : apply_queries("VACUUM" . ($_POST["optimize"] ? "" : " ANALYZE"), $_POST["tables"]));
            $Ke = lang(266);
        } elseif (!$_POST["tables"]) {
            $Ke = lang(9);
        } elseif ($H = queries(($_POST["optimize"] ? "OPTIMIZE" : ($_POST["check"] ? "CHECK" : ($_POST["repair"] ? "REPAIR" : "ANALYZE"))) . " TABLE " . implode(", ", array_map('idf_escape', $_POST["tables"])))) {
            while ($J = $H->fetch_assoc()) {
                $Ke .= "<b>" . h($J["Table"]) . "</b>: " . h($J["Msg_text"]) . "<br>";
            }
        }
        queries_redirect(substr(ME, 0, -1), $Ke, $H);
    }
    page_header(($_GET["ns"] == "" ? lang(38) . ": " . h(DB) : lang(78) . ": " . h($_GET["ns"])), $n, true);
    if ($b->homepage()) {
        if ($_GET["ns"] !== "") {
            echo "<h3 id='tables-views'>" . lang(267) . "</h3>\n";
            $Qh = tables_list();
            if (!$Qh) {
                echo "<p class='message'>" . lang(9) . "\n";
            } else {
                echo "<form action='' method='post'>\n";
                if (support("table")) {
                    echo "<fieldset><legend>" . lang(268) . " <span id='selected2'></span></legend><div>", "<input type='search' name='query' value='" . h($_POST["query"]) . "'>", script("qsl('input').onkeydown = partialArg(bodyKeydown, 'search');", ""), " <input type='submit' name='search' value='" . lang(57) . "'>\n", "</div></fieldset>\n";
                    if ($_POST["search"] && $_POST["query"] != "") {
                        $_GET["where"][0]["op"] = "LIKE %%";
                        search_tables();
                    }
                }
                $dc = doc_link(['sql' => 'show-table-status.html']);
                echo "<table cellspacing='0' class='nowrap checkable'>\n", script("mixin(qsl('table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true)});"), '<thead><tr class="wrap">', '<td><input id="check-all" type="checkbox" class="jsonly">' . script("qs('#check-all').onclick = partial(formCheck, /^(tables|views)\[/);", ""), '<th>' . lang(130), '<td>' . lang(269) . doc_link(['sql' => 'storage-engines.html']), '<td>' . lang(121) . doc_link(['sql' => 'charset-charsets.html', 'mariadb' => 'supported-character-sets-and-collations/']), '<td>' . lang(270) . $dc, '<td>' . lang(271) . $dc, '<td>' . lang(272) . $dc, '<td>' . lang(52) . doc_link(['sql' => 'example-auto-increment.html', 'mariadb' => 'auto_increment/']), '<td>' . lang(273) . $dc, (support("comment") ? '<td>' . lang(51) . $dc : ''), "</thead>\n";
                $T = 0;
                foreach ($Qh
                         as $C => $U) {
                    $Vi = ($U !== null && !preg_match('~table~i', $U));
                    $t = h("Table-" . $C);
                    echo '<tr' . odd() . '><td>' . checkbox(($Vi ? "views[]" : "tables[]"), $C, in_array($C, $Rh, true), "", "", "", $t), '<th>' . (support("table") || support("indexes") ? "<a href='" . h(ME) . "table=" . urlencode($C) . "' title='" . lang(43) . "' id='$t'>" . h($C) . '</a>' : h($C));
                    if ($Vi) {
                        echo '<td colspan="6"><a href="' . h(ME) . "view=" . urlencode($C) . '" title="' . lang(44) . '">' . (preg_match('~materialized~i', $U) ? lang(128) : lang(129)) . '</a>', '<td align="right"><a href="' . h(ME) . "select=" . urlencode($C) . '" title="' . lang(42) . '">?</a>';
                    } else {
                        foreach (["Engine" => [], "Collation" => [], "Data_length" => ["create", lang(45)], "Index_length" => ["indexes", lang(132)], "Data_free" => ["edit", lang(46)], "Auto_increment" => ["auto_increment=1&create", lang(45)], "Rows" => ["select", lang(42)],] as $y => $_) {
                            $t = " id='$y-" . h($C) . "'";
                            echo($_ ? "<td align='right'>" . (support("table") || $y == "Rows" || (support("indexes") && $y != "Data_length") ? "<a href='" . h(ME . "$_[0]=") . urlencode($C) . "'$t title='$_[1]'>?</a>" : "<span$t>?</span>") : "<td id='$y-" . h($C) . "'>");
                        }
                        $T++;
                    }
                    echo(support("comment") ? "<td id='Comment-" . h($C) . "'>" : "");
                }
                echo "<tr><td><th>" . lang(246, count($Qh)), "<td>" . h($x == "sql" ? $g->result("SELECT @@storage_engine") : ""), "<td>" . h(db_collation(DB, collations()));
                foreach (["Data_length", "Index_length", "Data_free"] as $y) {
                    echo "<td align='right' id='sum-$y'>";
                }
                echo "</table>\n";
                if (!information_schema(DB)) {
                    echo "<div class='footer'><div>\n";
                    $Pi = "<input type='submit' value='" . lang(274) . "'> " . on_help("'VACUUM'");
                    $tf = "<input type='submit' name='optimize' value='" . lang(275) . "'> " . on_help($x == "sql" ? "'OPTIMIZE TABLE'" : "'VACUUM OPTIMIZE'");
                    echo "<fieldset><legend>" . lang(125) . " <span id='selected'></span></legend><div>" . ($x == "sqlite" ? $Pi : ($x == "pgsql" ? $Pi . $tf : ($x == "sql" ? "<input type='submit' value='" . lang(276) . "'> " . on_help("'ANALYZE TABLE'") . $tf . "<input type='submit' name='check' value='" . lang(277) . "'> " . on_help("'CHECK TABLE'") . "<input type='submit' name='repair' value='" . lang(278) . "'> " . on_help("'REPAIR TABLE'") : ""))) . "<input type='submit' name='truncate' value='" . lang(279) . "'> " . on_help($x == "sqlite" ? "'DELETE'" : "'TRUNCATE" . ($x == "pgsql" ? "'" : " TABLE'")) . confirm() . "<input type='submit' name='drop' value='" . lang(126) . "'>" . on_help("'DROP TABLE'") . confirm() . "\n";
                    $k = (support("scheme") ? $b->schemas() : $b->databases());
                    if (count($k) != 1 && $x != "sqlite") {
                        $l = (isset($_POST["target"]) ? $_POST["target"] : (support("scheme") ? $_GET["ns"] : DB));
                        echo "<p>" . lang(280) . ": ", ($k ? html_select("target", $k, $l) : '<input name="target" value="' . h($l) . '" autocapitalize="off">'), " <input type='submit' name='move' value='" . lang(281) . "'>", (support("copy") ? " <input type='submit' name='copy' value='" . lang(282) . "'>" : ""), "\n";
                    }
                    echo "<input type='hidden' name='all' value=''>";
                    echo
                    script("qsl('input').onclick = function () { selectCount('selected', formChecked(this, /^(tables|views)\[/));" . (support("table") ? " selectCount('selected2', formChecked(this, /^tables\[/) || $T);" : "") . " }"), "<input type='hidden' name='token' value='$ki'>\n", "</div></fieldset>\n", "</div></div>\n";
                }
                echo "</form>\n", script("tableCheck();");
            }
            echo '<p class="links"><a href="' . h(ME) . 'create=">' . lang(75) . "</a>\n", (support("view") ? '<a href="' . h(ME) . 'view=">' . lang(203) . "</a>\n" : "");
            if (support("routine")) {
                echo "<h3 id='routines'>" . lang(142) . "</h3>\n";
                $Tg = routines();
                if ($Tg) {
                    echo "<table cellspacing='0'>\n", '<thead><tr><th>' . lang(182) . '<td>' . lang(50) . '<td>' . lang(220) . "<td></thead>\n";
                    odd('');
                    foreach ($Tg
                             as $J) {
                        $C = ($J["SPECIFIC_NAME"] == $J["ROUTINE_NAME"] ? "" : "&name=" . urlencode($J["ROUTINE_NAME"]));
                        echo '<tr' . odd() . '>', '<th><a href="' . h(ME . ($J["ROUTINE_TYPE"] != "PROCEDURE" ? 'callf=' : 'call=') . urlencode($J["SPECIFIC_NAME"]) . $C) . '">' . h($J["ROUTINE_NAME"]) . '</a>', '<td>' . h($J["ROUTINE_TYPE"]), '<td>' . h($J["DTD_IDENTIFIER"]), '<td><a href="' . h(ME . ($J["ROUTINE_TYPE"] != "PROCEDURE" ? 'function=' : 'procedure=') . urlencode($J["SPECIFIC_NAME"]) . $C) . '">' . lang(135) . "</a>";
                    }
                    echo "</table>\n";
                }
                echo '<p class="links">' . (support("procedure") ? '<a href="' . h(ME) . 'procedure=">' . lang(219) . '</a>' : '') . '<a href="' . h(ME) . 'function=">' . lang(218) . "</a>\n";
            }
            if (support("sequence")) {
                echo "<h3 id='sequences'>" . lang(283) . "</h3>\n";
                $hh = get_vals("SELECT sequence_name FROM information_schema.sequences WHERE sequence_schema = current_schema() ORDER BY sequence_name");
                if ($hh) {
                    echo "<table cellspacing='0'>\n", "<thead><tr><th>" . lang(182) . "</thead>\n";
                    odd('');
                    foreach ($hh
                             as $X) {
                        echo "<tr" . odd() . "><th><a href='" . h(ME) . "sequence=" . urlencode($X) . "'>" . h($X) . "</a>\n";
                    }
                    echo "</table>\n";
                }
                echo "<p class='links'><a href='" . h(ME) . "sequence='>" . lang(225) . "</a>\n";
            }
            if (support("type")) {
                echo "<h3 id='user-types'>" . lang(26) . "</h3>\n";
                $Ni = types();
                if ($Ni) {
                    echo "<table cellspacing='0'>\n", "<thead><tr><th>" . lang(182) . "</thead>\n";
                    odd('');
                    foreach ($Ni
                             as $X) {
                        echo "<tr" . odd() . "><th><a href='" . h(ME) . "type=" . urlencode($X) . "'>" . h($X) . "</a>\n";
                    }
                    echo "</table>\n";
                }
                echo "<p class='links'><a href='" . h(ME) . "type='>" . lang(229) . "</a>\n";
            }
            if (support("event")) {
                echo "<h3 id='events'>" . lang(143) . "</h3>\n";
                $K = get_rows("SHOW EVENTS");
                if ($K) {
                    echo "<table cellspacing='0'>\n", "<thead><tr><th>" . lang(182) . "<td>" . lang(284) . "<td>" . lang(209) . "<td>" . lang(210) . "<td></thead>\n";
                    foreach ($K
                             as $J) {
                        echo "<tr>", "<th>" . h($J["Name"]), "<td>" . ($J["Execute at"] ? lang(285) . "<td>" . $J["Execute at"] : lang(211) . " " . $J["Interval value"] . " " . $J["Interval field"] . "<td>$J[Starts]"), "<td>$J[Ends]", '<td><a href="' . h(ME) . 'event=' . urlencode($J["Name"]) . '">' . lang(135) . '</a>';
                    }
                    echo "</table>\n";
                    $Ac = $g->result("SELECT @@event_scheduler");
                    if ($Ac && $Ac != "ON") {
                        echo "<p class='error'><code class='jush-sqlset'>event_scheduler</code>: " . h($Ac) . "\n";
                    }
                }
                echo '<p class="links"><a href="' . h(ME) . 'event=">' . lang(208) . "</a>\n";
            }
            if ($Qh) {
                echo
            script("ajaxSetHtml('" . js_escape(ME) . "script=db');");
            }
        }
    }
}
page_footer();
