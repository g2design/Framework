<?php

/* templates/site_template.twig */
class __TwigTemplate_048c5b85182c29704d4e1b54932cdf66dc978a423a0ba7099ee5430a2c99944e extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!doctype html>
<html>
    <head>
\t\t<script>document.cookie = 'resolution=' + Math.max(screen.width, screen.height) + (\"devicePixelRatio\" in window ? \",\" + devicePixelRatio : \",1\") + '; path=/';</script>
\t\t<meta charset=\"UTF-8\">
\t\t<meta name=\"viewport\" content=\"width=device-width, height=device-height, initial-scale=1.0\">
\t\t<base href=\"";
        // line 7
        echo (isset($context["base_url"]) ? $context["base_url"] : null);
        echo "\">
\t\t<title>";
        // line 8
        echo (isset($context["meta_title"]) ? $context["meta_title"] : null);
        echo "</title>
\t\t<meta name =\"description\" content=\"";
        // line 9
        echo (isset($context["meta_description"]) ? $context["meta_description"] : null);
        echo "\">
\t\t";
        // line 10
        echo (isset($context["meta_extra"]) ? $context["meta_extra"] : null);
        echo "

\t\t<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
\t\t<!--[if lt IE 9]>
\t\t  <script type='text/javascript' src=\"http://html5shiv.googlecode.com/svn/trunk/html5.js\"></script>
\t\t  <script type='text/javascript' src=\"//cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.js\"></script>
\t\t  <script>
\t\t  if ( window.console === undefined ) {

\t\t\twindow.console = {
\t\t\t\tlog : function(){}
\t\t\t}

\t\t\t}
\t\t\t</script>
\t\t<![endif]-->

    </head>

    <body>
\t\t";
        // line 30
        echo (isset($context["content"]) ? $context["content"] : null);
        echo "
    </body>
</html>
";
    }

    public function getTemplateName()
    {
        return "templates/site_template.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  62 => 30,  39 => 10,  35 => 9,  31 => 8,  27 => 7,  19 => 1,);
    }
}
