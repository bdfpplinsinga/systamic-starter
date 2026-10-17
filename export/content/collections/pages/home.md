---
id: home
blueprint: page
title: Home
page_builder:
  -
    id: t1
    type: text
    enabled: true
    width: narrow
    text:
      -
        type: heading
        attrs:
          level: 2
        content:
          -
            type: text
            text: Welkom
      -
        type: paragraph
        content:
          -
            type: text
            text: 'Deze site is gestart vanuit de Systamic starter kit.'
  -
    id: c1
    type: call_to_action
    enabled: true
    background: muted
    anchor: contact
    eyebrow: 'Klaar om te starten?'
    heading: 'Neem contact op'
    heading_level: h2
    text:
      -
        type: paragraph
        content:
          -
            type: text
            text: 'We denken graag met je mee.'
    buttons:
      -
        id: b1
        label: Contact
        variant: primary
        link: 'mailto:info@example.com'
      -
        id: b2
        label: 'Meer lezen'
        variant: ghost
        link: 'entry::home'
---
