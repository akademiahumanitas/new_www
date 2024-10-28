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
class __TwigTemplate_319d17fe6e8abeae0fb14f6f0a2b8acccee8331647970281963f18b264f35b16 extends \WPML\Core\Twig\Template
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
        $context["css_classes_flag"] = \WPML\Core\twig_trim_filter(("wpml-ls-flag " . $this->getAttribute(($context["backward_compatibility"] ?? null), "css_classes_flag", [])));
        // line 2
        $context["css_classes_native"] = \WPML\Core\twig_trim_filter(("wpml-ls-native " . $this->getAttribute(($context["backward_compatibility"] ?? null), "css_classes_native", [])));
        // line 3
        $context["css_classes_display"] = \WPML\Core\twig_trim_filter(("wpml-ls-display " . $this->getAttribute(($context["backward_compatibility"] ?? null), "css_classes_display", [])));
        // line 4
        $context["css_classes_bracket"] = \WPML\Core\twig_trim_filter(("wpml-ls-bracket " . $this->getAttribute(($context["backward_compatibility"] ?? null), "css_classes_bracket", [])));
        // line 5
        $context["css_classes_link"] = \WPML\Core\twig_trim_filter(((($context["css_classes_link"] ?? null) . " ") . $this->getAttribute($this->getAttribute(($context["language"] ?? null), "backward_compatibility", []), "css_classes_a", [])));
        // line 6
        echo "
";
        // line 7
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["languages"] ?? null));
        $context['loop'] = [
          'parent' => $context['_parent'],
          'index0' => 0,
          'index'  => 1,
          'first'  => true,
        ];
        if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof \Countable)) {
            $length = count($context['_seq']);
            $context['loop']['revindex0'] = $length - 1;
            $context['loop']['revindex'] = $length;
            $context['loop']['length'] = $length;
            $context['loop']['last'] = 1 === $length;
        }
        foreach ($context['_seq'] as $context["code"] => $context["language"]) {
            // line 8
            echo "    ";
            ob_start(function () { return ''; });
            // line 9
            echo "    <span class=\"";
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["language"], "css_classes", []), "html", null, true);
            echo " wpml-ls-item-legacy-post-translations\">
        <a href=\"";
            // line 10
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["language"], "url", []), "html", null, true);
            echo "\" class=\"";
            echo \WPML\Core\twig_escape_filter($this->env, ($context["css_classes_link"] ?? null), "html", null, true);
            echo "\">
            ";
            // line 11
            $this->loadTemplate("flag.twig", "template.twig", 11)->display($context);
            // line 13
            if (($this->getAttribute($context["language"], "is_current", []) && ($this->getAttribute($context["language"], "native_name", []) || $this->getAttribute($context["language"], "display_name", [])))) {
                // line 15
                $context["current_language_name"] = (($this->getAttribute($context["language"], "native_name", [], "any", true, true)) ? (\WPML\Core\_twig_default_filter($this->getAttribute($context["language"], "native_name", []), $this->getAttribute($context["language"], "display_name", []))) : ($this->getAttribute($context["language"], "display_name", [])));
                // line 16
                echo "<span class=\"";
                echo \WPML\Core\twig_escape_filter($this->env, ($context["css_classes_native"] ?? null), "html", null, true);
                echo "\">";
                echo \WPML\Core\twig_escape_filter($this->env, ($context["current_language_name"] ?? null), "html", null, true);
                echo "</span>";
            } else {
                // line 20
                if ($this->getAttribute($context["language"], "native_name", [])) {
                    // line 21
                    echo "<span class=\"";
                    echo \WPML\Core\twig_escape_filter($this->env, ($context["css_classes_native"] ?? null), "html", null, true);
                    echo "\" lang=\"";
                    echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["language"], "code", []), "html", null, true);
                    echo "\">";
                    echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["language"], "native_name", []), "html", null, true);
                    echo "</span>";
                }
                // line 24
                if (($this->getAttribute($context["language"], "display_name", []) && ($this->getAttribute($context["language"], "display_name", []) != $this->getAttribute($context["language"], "native_name", [])))) {
                    // line 25
                    echo "<span class=\"";
                    echo \WPML\Core\twig_escape_filter($this->env, ($context["css_classes_display"] ?? null), "html", null, true);
                    echo "\">";
                    // line 26
                    if ($this->getAttribute($context["language"], "native_name", [])) {
                        echo "<span class=\"";
                        echo \WPML\Core\twig_escape_filter($this->env, ($context["css_classes_bracket"] ?? null), "html", null, true);
                        echo "\"> (</span>";
                    }
                    // line 27
                    echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($context["language"], "display_name", []), "html", null, true);
                    // line 28
                    if ($this->getAttribute($context["language"], "native_name", [])) {
                        echo "<span class=\"";
                        echo \WPML\Core\twig_escape_filter($this->env, ($context["css_classes_bracket"] ?? null), "html", null, true);
                        echo "\">)</span>";
                    }
                    // line 29
                    echo "</span>";
                }
            }
            // line 33
            echo "</a>
    </span>
    ";
            echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
            ++$context['loop']['index0'];
            ++$context['loop']['index'];
            $context['loop']['first'] = false;
            if (isset($context['loop']['length'])) {
                --$context['loop']['revindex0'];
                --$context['loop']['revindex'];
                $context['loop']['last'] = 0 === $context['loop']['revindex0'];
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['code'], $context['language'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
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
        return array (  124 => 33,  120 => 29,  114 => 28,  112 => 27,  106 => 26,  102 => 25,  100 => 24,  91 => 21,  89 => 20,  82 => 16,  80 => 15,  78 => 13,  76 => 11,  70 => 10,  65 => 9,  62 => 8,  45 => 7,  42 => 6,  40 => 5,  38 => 4,  36 => 3,  34 => 2,  32 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "template.twig", "/var/www/html/wp-content/plugins/sitepress-multilingual-cms/templates/language-switchers/legacy-post-translations/template.twig");
    }
}
