<?php

/* - Widget related exceptions - */
class DescriptorDoesNotExist extends Exception {}
class WidgetDoesNotExist extends Exception {}

class BadPosition extends Exception {}

class DataExceptions extends Exception {}
class InvalidData extends DataExceptions {}
class MissingData extends DataExceptions {}
class EmptyData extends DataExceptions {}
