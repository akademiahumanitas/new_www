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

/* flag.twig */
class __TwigTemplate_b5f25b0fee43abab6b88696d6252b8acf08fabddfa5d135f7d9f464097c49170 extends \WPML\Core\Twig\Template
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
        if ($this->getAttribute(($context["language"] ?? null), "flag_url", [])) {
            // line 2
            echo "    ";
            if (($this->getAttribute(($context["language"] ?? null), "flag_width", []) > 0)) {
                // line 3
                echo "        ";
                $context["flag_width"] = sprintf("width=%s", $this->getAttribute(($context["language"] ?? null), "flag_width", []));
                // line 4
                echo "    ";
            }
            // line 5
            echo "    ";
            if (($this->getAttribute(($context["language"] ?? null), "flag_height", []) > 0)) {
                // line 6
                echo "        ";
                $context["flag_height"] = sprintf("height=%s", $this->getAttribute(($context["language"] ?? null), "flag_height", []));
                // line 7
                echo "    ";
            }
            // line 8
            echo "    <img
            class=\"";
            // line 9
            echo \WPML\Core\twig_escape_filter($this->env, ($context["css_classes_flag"] ?? null), "html", null, true);
            echo "\"
            src=\"";
            // line 10
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["language"] ?? null), "flag_url", []), "html", null, true);
            echo "\"
            alt=\"";
            // line 11
            echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["language"] ?? null), "flag_alt", []), "html", null, true);
            echo "\"
            ";
            // line 12
            echo \WPML\Core\twig_escape_filter($this->env, ($context["flag_width"] ?? null), "html", null, true);
            echo "
            ";
            // line 13
            echo \WPML\Core\twig_escape_filter($this->env, ($context["flag_height"] ?? null), "html", null, true);
            echo "
    />";
        }
    }

    public function getTemplateName()
    {
        return "flag.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  71 => 13,  67 => 12,  63 => 11,  59 => 10,  55 => 9,  52 => 8,  49 => 7,  46 => 6,  43 => 5,  40 => 4,  37 => 3,  34 => 2,  32 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "flag.twig", "/var/www/html/wp-content/plugins/sitepress-multilingual-cms/templates/language-switchers/flag.twig");
    }
}
