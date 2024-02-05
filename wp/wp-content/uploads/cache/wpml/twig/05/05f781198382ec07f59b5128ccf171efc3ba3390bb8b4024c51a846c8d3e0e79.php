<?php

namespace WPML\Core;

use \WPML\Core\Twig\Environment;
use \WPML\Core\Twig\Error\LoaderError;
use \WPML\Core\Twig\Error\RuntimeError;
use \WPML\Core\Twig\Markup;
use \WPML\Core\Twig\Sandbox\SecurityError;
use \WPML\Core\Twig\Sandbox\SecurityNotAllowedTagError;
use \WPML\Core\Twig\Sandbox\SecurityNotAllowedFilterError;
use \WPML\Core\Twig\Sandbox\SecurityNotAllowedFunctionError;
use \WPML\Core\Twig\Source;
use \WPML\Core\Twig\Template;

/* template.twig */
class __TwigTemplate_bf0b8c3a8e7b93be005867a8fa0083777c12c420ab466ec26bdfdda6ac32e553 extends \WPML\Core\Twig\Template
{
    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        // line 1
        $context["current_language"] = $this->getAttribute(($context["languages"] ?? null), ($context["current_language_code"] ?? null), [], "array");
        // line 2
        $context["css_classes_flag"] = \WPML\Core\twig_trim_filter(("wpml-ls-flag " . $this->getAttribute(($context["backward_compatibility"] ?? null), "css_classes_flag", [])));
        // line 3
        $context["css_classes_native"] = \WPML\Core\twig_trim_filter(("wpml-ls-native " . $this->getAttribute(($context["backward_compatibility"] ?? null), "css_classes_native", [])));
        // line 4
        $context["css_classes_display"] = \WPML\Core\twig_trim_filter(("wpml-ls-display " . $this->getAttribute(($context["backward_compatibility"] ?? null), "css_classes_display", [])));
        // line 5
        $context["css_classes_bracket"] = \WPML\Core\twig_trim_filter(("wpml-ls-bracket " . $this->getAttribute(($context["backward_compatibility"] ?? null), "css_classes_bracket", [])));
        // line 6
        echo "
<div
\t class=\"";
        // line 8
        echo \WPML\Core\twig_escape_filter($this->env, ($context["css_classes"] ?? null), "html", null, true);
        echo " wpml-ls-legacy-dropdown js-wpml-ls-legacy-dropdown\"";
        if ($this->getAttribute(($context["backward_compatibility"] ?? null), "css_id", [])) {
            echo " id=\"";
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["backward_compatibility"] ?? null), "css_id", []), "html", null, true);
            echo "\"";
        }
        echo ">
\t<ul>

\t\t<li tabindex=\"0\" class=\"";
        // line 11
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["current_language"] ?? null), "css_classes", []), "html", null, true);
        echo " wpml-ls-item-legacy-dropdown\">
\t\t\t<a href=\"#\" class=\"";
        // line 12
        echo \WPML\Core\twig_escape_filter($this->env, \WPML\Core\twig_trim_filter(("js-wpml-ls-item-toggle wpml-ls-item-toggle " . $this->getAttribute($this->getAttribute(($context["current_language"] ?? null), "backward_compatibility", []), "css_classes_a", []))), "html", null, true);
        echo "\">
                ";
        // line 13
        $this->loadTemplate("flag.twig", "template.twig", 13)->display(twig_array_merge($context, ["language" => ($context["current_language"] ?? null), "css_classes_flag" => ($context["css_classes_flag"] ?? null)]));
        // line 15
        if (($this->getAttribute(($context["current_language"] ?? null), "display_name", []) || $this->getAttribute(($context["current_language"] ?? null), "native_name", []))) {
            // line 16
            $context["current_language_name"] = (($this->getAttribute(($context["current_language"] ?? null), "display_name", [], "any", true, true)) ? (\WPML\Core\_twig_default_filter($this->getAttribute(($context["current_language"] ?? null), "display_name", []), $this->getAttribute(($context["current_language"] ?? null), "native_name", []))) : ($this->getAttribute(($context["current_language"] ?? null), "native_name", [])));
            // line 17
            echo "<span class=\"";
            echo \WPML\Core\twig_escape_filter($this->env, ($context["css_classes_native"] ?? null), "html", null, true);
            echo "\">";
            echo \WPML\Core\twig_escape_filter($this->env, ($context["current_language_name"] ?? null), "html", null, true);
            echo "</span>";
        }
        // line 19
        echo "</a>

\t\t\t<ul class=\"wpml-ls-sub-menu\">
\t\t\t\t";
        // line 22
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["languages"] ?? null));
        $context['loop'] = [
          'parent' => $context['_parent'],
          'index0' => 0,
          'index'  => 1,
          'first'  => true,
        ];
        foreach ($context['_seq'] as $context["_key"] => $context["language"]) {
            if ( !$this->getAttribute($context["language"], "is_current", [])) {
                // line 23
                echo "
\t\t\t\t\t<li class=\"";
                // line 24
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["language"], "css_classes", []), "html", null, true);
                echo "\">
\t\t\t\t\t\t<a href=\"";
                // line 25
                echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["language"], "url", []), "html", null, true);
                echo "\" class=\"";
                echo \WPML\Core\twig_escape_filter($this->env, ($context["css_classes_link"] ?? null), "html", null, true);
                echo "\">
                            ";
                // line 26
                $this->loadTemplate("flag.twig", "template.twig", 26)->display($context);
                // line 28
                if ($this->getAttribute($context["language"], "native_name", [])) {
                    // line 29
                    echo "<span class=\"";
                    echo \WPML\Core\twig_escape_filter($this->env, ($context["css_classes_native"] ?? null), "html", null, true);
                    echo "\" lang=\"";
                    echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["language"], "code", []), "html", null, true);
                    echo "\">";
                    echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["language"], "native_name", []), "html", null, true);
                    echo "</span>";
                }
                // line 31
                if (($this->getAttribute($context["language"], "display_name", []) && ($this->getAttribute($context["language"], "display_name", []) != $this->getAttribute($context["language"], "native_name", [])))) {
                    // line 32
                    echo "<span class=\"";
                    echo \WPML\Core\twig_escape_filter($this->env, ($context["css_classes_display"] ?? null), "html", null, true);
                    echo "\">";
                    // line 33
                    if ($this->getAttribute($context["language"], "native_name", [])) {
                        echo "<span class=\"";
                        echo \WPML\Core\twig_escape_filter($this->env, ($context["css_classes_bracket"] ?? null), "html", null, true);
                        echo "\"> (</span>";
                    }
                    // line 34
                    echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["language"], "display_name", []), "html", null, true);
                    // line 35
                    if ($this->getAttribute($context["language"], "native_name", [])) {
                        echo "<span class=\"";
                        echo \WPML\Core\twig_escape_filter($this->env, ($context["css_classes_bracket"] ?? null), "html", null, true);
                        echo "\">)</span>";
                    }
                    // line 36
                    echo "</span>";
                }
                // line 38
                echo "</a>
\t\t\t\t\t</li>

\t\t\t\t";
                ++$context['loop']['index0'];
                ++$context['loop']['index'];
                $context['loop']['first'] = false;
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['language'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 42
        echo "\t\t\t</ul>

\t\t</li>

\t</ul>
</div>
";
    }

    public function getTemplateName()
    {
        return "template.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  157 => 42,  144 => 38,  141 => 36,  135 => 35,  133 => 34,  127 => 33,  123 => 32,  121 => 31,  112 => 29,  110 => 28,  108 => 26,  102 => 25,  98 => 24,  95 => 23,  84 => 22,  79 => 19,  72 => 17,  70 => 16,  68 => 15,  66 => 13,  62 => 12,  58 => 11,  46 => 8,  42 => 6,  40 => 5,  38 => 4,  36 => 3,  34 => 2,  32 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "template.twig", "/var/www/html/wp-content/plugins/sitepress-multilingual-cms/templates/language-switchers/legacy-dropdown/template.twig");
    }
}
