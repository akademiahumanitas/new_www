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

/* checkboxes-includes.twig */
class __TwigTemplate_e14be13d83896b61b052ee64f6c88e0fd2d8bc1da345e2ccd625d49baa175ec9 extends \WPML\Core\Twig\Template
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
        $context["force"] = $this->getAttribute($this->getAttribute($this->getAttribute(($context["data"] ?? null), "templates", []), ($context["template_slug"] ?? null), [], "array"), "force_settings", []);
        // line 2
        $context["is_hierarchical"] = (($this->getAttribute(($context["slot_settings"] ?? null), "slot_group", []) == "menus") && $this->getAttribute(($context["slot_settings"] ?? null), "is_hierarchical", []));
        // line 3
        echo "
<h4>";
        // line 4
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["strings"] ?? null), "misc", []), "title_what_to_include", []), "html", null, true);
        echo " ";
        $this->loadTemplate("tooltip.twig", "checkboxes-includes.twig", 4)->display(twig_array_merge($context, ["content" => $this->getAttribute($this->getAttribute(($context["strings"] ?? null), "tooltips", []), "what_to_include", [])]));
        echo "</h4>
<ul class=\"js-wpml-ls-to-include\">
    <li>
        <label>
            <input
                    type=\"checkbox\"
                    class=\"js-wpml-ls-setting-display_flags js-wpml-ls-toggle-suboptions js-wpml-ls-trigger-update\"
                    data-target=\".js-wpml-ls-flag-sizes\"
                    data-show-on-checked=\"1\"
                    value=\"1\"
                    name=\"";
        // line 14
        if (($context["name_base"] ?? null)) {
            echo \WPML\Core\twig_escape_filter($this->env, ($context["name_base"] ?? null), "html", null, true);
            echo "[display_flags]";
        } else {
            echo "display_flags";
        }
        echo "\"
                    ";
        // line 15
        if ($this->getAttribute(($context["force"] ?? null), "display_flags", [], "any", true, true)) {
            echo " disabled=\"disabled\"";
        }
        // line 16
        echo "                    ";
        if ($this->getAttribute(($context["slot_settings"] ?? null), "display_flags", [])) {
            echo " checked=\"checked\"";
        }
        // line 17
        echo "            > ";
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["strings"] ?? null), "misc", []), "label_include_flag", []), "html", null, true);
        echo "
        </label>
        <ul class=\"js-wpml-ls-flag-sizes\"
            style=\"display: ";
        // line 20
        if ($this->getAttribute(($context["slot_settings"] ?? null), "display_flags", [])) {
            echo "block";
        } else {
            echo "none";
        }
        echo ";\">
            <li>
                <label>
                    ";
        // line 23
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["strings"] ?? null), "misc", []), "label_include_flag_width", []), "html", null, true);
        echo "
                    <input
                            type=\"number\"
                            class=\"js-wpml-ls-setting-include_flag_width js-wpml-ls-trigger-update\"
                            name=\"";
        // line 27
        if (($context["name_base"] ?? null)) {
            echo \WPML\Core\twig_escape_filter($this->env, ($context["name_base"] ?? null), "html", null, true);
            echo "[include_flag_width]";
        } else {
            echo "include_flag_width";
        }
        echo "\"
                            value=\"";
        // line 28
        ((($this->getAttribute(($context["slot_settings"] ?? null), "include_flag_width", []) > 0)) ? (print (\WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["slot_settings"] ?? null), "include_flag_width", []), "html", null, true))) : (print ("")));
        echo "\"
                            placeholder=\"";
        // line 29
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["strings"] ?? null), "misc", []), "label_include_flag_width_placeholder", []), "html", null, true);
        echo "\"
                    >
                    ";
        // line 31
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["strings"] ?? null), "misc", []), "label_include_flag_width_suffix", []), "html", null, true);
        echo "
                </label>
            </li>
            <li>
                <label>
                    ";
        // line 36
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["strings"] ?? null), "misc", []), "label_include_flag_height", []), "html", null, true);
        echo "
                    <input
                            type=\"number\"
                            class=\"js-wpml-ls-setting-_include_flag_height js-wpml-ls-trigger-update\"
                            name=\"";
        // line 40
        if (($context["name_base"] ?? null)) {
            echo \WPML\Core\twig_escape_filter($this->env, ($context["name_base"] ?? null), "html", null, true);
            echo "[include_flag_height]";
        } else {
            echo "include_flag_height";
        }
        echo "\"
                            value=\"";
        // line 41
        ((($this->getAttribute(($context["slot_settings"] ?? null), "include_flag_height", []) > 0)) ? (print (\WPML\Core\twig_escape_filter($this->env, $this->getAttribute(($context["slot_settings"] ?? null), "include_flag_height", []), "html", null, true))) : (print ("")));
        echo "\"
                            placeholder=\"";
        // line 42
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["strings"] ?? null), "misc", []), "label_include_flag_height_placeholder", []), "html", null, true);
        echo "\"
                    >
                    ";
        // line 44
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["strings"] ?? null), "misc", []), "label_include_flag_height_suffix", []), "html", null, true);
        echo "
                </label>
            </li>
        </ul>
    </li>
    <li>
        <label><input type=\"checkbox\" class=\"js-wpml-ls-setting-display_names_in_native_lang js-wpml-ls-trigger-update\"
                      name=\"";
        // line 51
        if (($context["name_base"] ?? null)) {
            echo \WPML\Core\twig_escape_filter($this->env, ($context["name_base"] ?? null), "html", null, true);
            echo "[display_names_in_native_lang]";
        } else {
            echo "display_names_in_native_lang";
        }
        echo "\"
                    ";
        // line 52
        if ($this->getAttribute(($context["force"] ?? null), "display_names_in_native_lang", [], "any", true, true)) {
            echo " disabled=\"disabled\"";
        }
        // line 53
        echo "                      value=\"1\"";
        if ($this->getAttribute(($context["slot_settings"] ?? null), "display_names_in_native_lang", [])) {
            echo " checked=\"checked\"";
        }
        echo "> ";
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["strings"] ?? null), "misc", []), "label_include_native_lang", []), "html", null, true);
        echo "
        </label>
    </li>
    <li>
        <label><input type=\"checkbox\" class=\"js-wpml-ls-setting-display_names_in_current_lang js-wpml-ls-trigger-update\"
                      name=\"";
        // line 58
        if (($context["name_base"] ?? null)) {
            echo \WPML\Core\twig_escape_filter($this->env, ($context["name_base"] ?? null), "html", null, true);
            echo "[display_names_in_current_lang]";
        } else {
            echo "display_names_in_current_lang";
        }
        echo "\"
                    ";
        // line 59
        if ($this->getAttribute(($context["force"] ?? null), "display_names_in_current_lang", [], "any", true, true)) {
            echo " disabled=\"disabled\"";
        }
        // line 60
        echo "                      value=\"1\"";
        if ((($this->getAttribute(($context["slot_settings"] ?? null), "display_names_in_current_lang", [], "any", true, true)) ? (\WPML\Core\_twig_default_filter($this->getAttribute(($context["slot_settings"] ?? null), "display_names_in_current_lang", []), 1)) : (1))) {
            echo " checked=\"checked\"";
        }
        echo "> ";
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["strings"] ?? null), "misc", []), "label_include_display_lang", []), "html", null, true);
        echo "
        </label>
    </li>
    <li>
        <label><input type=\"checkbox\" class=\"js-wpml-ls-setting-display_link_for_current_lang js-wpml-ls-trigger-update\"
                      name=\"";
        // line 65
        if (($context["name_base"] ?? null)) {
            echo \WPML\Core\twig_escape_filter($this->env, ($context["name_base"] ?? null), "html", null, true);
            echo "[display_link_for_current_lang]";
        } else {
            echo "display_link_for_current_lang";
        }
        echo "\"
                    ";
        // line 66
        if (($this->getAttribute(($context["force"] ?? null), "display_link_for_current_lang", [], "any", true, true) || ($context["is_hierarchical"] ?? null))) {
            echo " disabled=\"disabled\"";
        }
        // line 67
        echo "                      value=\"1\"";
        if ((($this->getAttribute(($context["slot_settings"] ?? null), "display_link_for_current_lang", [], "any", true, true)) ? (\WPML\Core\_twig_default_filter($this->getAttribute(($context["slot_settings"] ?? null), "display_link_for_current_lang", []), 1)) : (1))) {
            echo " checked=\"checked\"";
        }
        echo "> ";
        echo \WPML\Core\twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["strings"] ?? null), "misc", []), "label_include_current_lang", []), "html", null, true);
        echo "
        </label>
    </li>
</ul>";
    }

    public function getTemplateName()
    {
        return "checkboxes-includes.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  222 => 67,  218 => 66,  209 => 65,  196 => 60,  192 => 59,  183 => 58,  170 => 53,  166 => 52,  157 => 51,  147 => 44,  142 => 42,  138 => 41,  129 => 40,  122 => 36,  114 => 31,  109 => 29,  105 => 28,  96 => 27,  89 => 23,  79 => 20,  72 => 17,  67 => 16,  63 => 15,  54 => 14,  39 => 4,  36 => 3,  34 => 2,  32 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "checkboxes-includes.twig", "/var/www/html/wp-content/plugins/sitepress-multilingual-cms/templates/language-switcher-admin-ui/checkboxes-includes.twig");
    }
}
