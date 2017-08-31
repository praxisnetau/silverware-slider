<% if $ColorPrimary %>

{$CSSID} .lSPager.lSGallery li.active a img {
  border-color: {$ColorPrimary};
}

{$CSSID} .lSPager.lSpg li.active a,
{$CSSID} .lSPager.lSpg li:hover a {
  background-color: {$ColorPrimary};
}

<% end_if %>

<% if $ColorSecondary %>

{$CSSID} .lSPager.lSGallery li:hover a img {
  border-color: {$ColorSecondary};
}

{$CSSID} .lSPager.lSpg li a {
  background-color: {$ColorSecondary};
}

<% end_if %>

<% if $ColorControl %>

{$CSSID} .lSAction > a i {
  color: {$ColorControl};
}

<% end_if %>
