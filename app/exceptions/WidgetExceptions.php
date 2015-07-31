<?php

/* - Widget related exceptions - */
class DescriptorDoesNotExist extends Exception {}
class WidgetDoesNotExist extends Exception {}

class BadPosition extends Exception {}

class DataException extends Exception {}
class InvalidData extends DataException {}
class MissingData extends DataException {}
class EmptyData extends DataException {}
