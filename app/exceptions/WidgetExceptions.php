<?php

/* - Widget related exceptions - */
class DescriptorDoesNotExist extends Exception {}
class WidgetDoesNotExist extends Exception {}

class BadPosition extends Exception {}

class WidgetException extends Exception {}
class WidgetFatalException extends WidgetException {}
