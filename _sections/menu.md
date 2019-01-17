---
title: Intro
cover-photo: assets/images/banner.jpg
cover-photo-alt: example cover photo
auto-header: none
icon: fa-comment
order: 1
---
<header>
	<h4>分类目录</h4>
	<p>
		<ul>
		    {%- for category in site.categories -%}
			    <li>
			    	<a href="" title="view all posts">{{ category | first }} {{ category | last | size }}</a>
			    </li>
		    {%- endfor -%}
		</ul>
	</p>
</header>