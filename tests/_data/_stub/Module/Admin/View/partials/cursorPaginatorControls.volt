<nav class="btn-group" role="group" aria-label="{{ 'Navigate records'|t }}">
    {% if false === (isFirst or isRewound) %}
    {{ link_to(prevUrl, '<i class="fa fa-angle-left"></i> '  ~ 'Previous'|t, 'class': 'btn btn-light', 'role': 'button') }}
    {% endif %}
    {% if true === (isPageable or isRewound) %}
    {{ link_to(nextUrl, 'Next'|t ~ ' <i class="fa fa-angle-right"></i>', 'class': 'btn btn-light', 'role': 'button') }}
    {% endif %}
</nav>
