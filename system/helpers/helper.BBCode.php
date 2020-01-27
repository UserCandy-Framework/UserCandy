<?php
/**
* BBCode Plugin
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version uc 1.0.4
*/

namespace Helpers;

class BBCode
{

    /** Convert BBCode to HTML Output **/
    public static function getHtml($str){
      $bb[] = "#\[b\](.*?)\[/b\]#si";
      $html[] = "<strong>\\1</strong>";
      $bb[] = "#\[i\](.*?)\[/i\]#si";
      $html[] = "<i>\\1</i>";
      $bb[] = "#\[u\](.*?)\[/u\]#si";
      $html[] = "<u>\\1</u>";
      $bb[] = "#\[s\](.*?)\[/s\]#si";
      $html[] = "<span style='text-decoration: line-through;'>\\1</span>";
      $bb[] = "#\[quote\](.*?)\[/quote\]#si";
      $html[] = "<div class='codeblock'><div class='epboxc' width='80%' align='left'><b><i>Quote</i></b><br><b>&ldquo;</b><i>\\1</i><b>&rdquo;</b></div></div>";
      $bb[] = "#\[youtube\](.*?)\[/youtube\]#si";
      $html[] = '<center><iframe width="300" height="169" src="//www.youtube.com/embed/\\1" frameborder="0" allowfullscreen></iframe></center>';
      $bb[] = "#\[hr\]#si";
      $html[] = "<hr>";
      $bb[] = "#\[code\](.*?)\[/code\]#si";
      $html[] = "<div class='codeblock'><div class='php' width='' align='left'><pre class='prettyprint'><b><i><font size=0.5>Code</font></i></b><pre class='pre-scrollable'><code>\\1</code></pre></div></div>";
      $str = str_replace('https://youtu.be/','',$str);
      $str = preg_replace ($bb, $html, $str);
      $patern = "#\[url=([^\]]*)\]([^\[]*)\[/url\]#i";
      $replace = '<a href="\\1" target="_blank" rel="nofollow">\\2</a>';
      $str = preg_replace($patern, $replace, $str);
      $patern = "#\[url\]([^\[]*)\[/url\]#i";
      $replace = '<a href="\\1" target="_blank" rel="nofollow">\\1</a>';
      $str = preg_replace($patern, $replace, $str);
      $patern = "#\[img\]([^\[]*)\[/img\]#i";
      $replace = '<img src="\\1" alt="" class="forum_img"/>';
      $str = preg_replace($patern, $replace, $str);
      $patern = "#\[color=([^\]]*)\]([^\[]*)\[/color\]#i";
      $replace = '<span style="color:\\1;">\\2</span>';
      $str = preg_replace($patern, $replace, $str);
      return $str;
    }

    /** BBCode Buttons **/
    public static function displayButtons($id_name = null){
      return "
        <!-- BBCode Buttons -->
        <div class='btn-group'>
          <button type=\"button\" class=\"btn btn-sm btn-light\" onclick=\"wrapText('edit','[b]','[/b]');\" title=\"Bold\"><i class='fas fa-bold'></i></button>
          <button type=\"button\" class=\"btn btn-sm btn-light\" onclick=\"wrapText('edit','[i]','[/i]');\" title=\"Italic\"><i class='fas fa-italic'></i></button>
          <button type=\"button\" class=\"btn btn-sm btn-light\" onclick=\"wrapText('edit','[u]','[/u]');\" title=\"Underline\"><i class='fas fa-underline'></i></button>
          <button type=\"button\" class=\"btn btn-sm btn-light\" onclick=\"wrapText('edit','[s]','[/s]');\" title=\"Strikethrough\"><i class='fas fa-strikethrough'></i></button>
          <button type=\"button\" class=\"btn btn-sm btn-light\" onclick=\"wrapText('edit','[color=]','[/color]');\" title=\"Color\"><i class='fas fa-paint-brush'></i></button>
        </div>
        <div class='btn-group'>
          <button type=\"button\" class=\"btn btn-sm btn-light\" onclick=\"wrapText('edit','[hr]','');\" title=\"Horizontal Rule\"><b>hr</b></button>
          <button type=\"button\" class=\"btn btn-sm btn-light\" onclick=\"wrapText('edit','[quote]','[/quote]');\" title=\"Quote\"><i class='fas fa-quote-right'></i></button>
          <button type=\"button\" class=\"btn btn-sm btn-light\" onclick=\"wrapText('edit','[code]','[/code]');\" title=\"Code\"><i class='fas fa-code'></i></button>
        </div>
        <div class='btn-group'>
          <button type=\"button\" class=\"btn btn-sm btn-light\" onclick=\"wrapText('edit','[url]','[/url]');\" title=\"Link\"><i class='fas fa-link'></i></button>
          <button type=\"button\" class=\"btn btn-sm btn-light\" onclick=\"wrapText('edit','[img]','[/img]');\" title=\"Image\"><i class='fas fa-image'></i></button>
          <button type=\"button\" class=\"btn btn-sm btn-light\" onclick=\"wrapText('edit','[youtube]','[/youtube]');\" title=\"YouTube\"><i class='fab fa-youtube'></i></button>
        </div>
        <script>
        function wrapText(elementID, openTag, closeTag) {
            var textArea = document.getElementById('$id_name');

            if (typeof(textArea.selectionStart) != \"undefined\") {
                var begin = textArea.value.substr(0, textArea.selectionStart);
                var selection = textArea.value.substr(textArea.selectionStart, textArea.selectionEnd - textArea.selectionStart);
                var end = textArea.value.substr(textArea.selectionEnd);
                textArea.value = begin + openTag + selection + closeTag + end;
            }
        }
        </script>
      ";
    }

}
