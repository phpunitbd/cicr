var ICRC = {
  unit_validator_data: 0,
  delegation_validator_data: 0,
  unit_delegation_validator_data: 0,
  oc_validator_data: 0,
  cc_validator_data: 0
};

/* custom validators */
Validation.add('radar-validate-unit', 'The unit does not exist', function(v) {
  return ICRC.unit_validator_data && ICRC.unit_validator_data[v];
});
Validation.add('radar-validate-delegation', 'The delegation does not exist', function(v) {
  return ICRC.delegation_validator_data && ICRC.delegation_validator_data[v];
});
Validation.add('radar-validate-unit-delegation', 'The unit or delegation does not exist', function(v) {
  return ICRC.unit_delegation_validator_data && ICRC.unit_delegation_validator_data[v];
});
Validation.add('radar-validate-objective-code', 'The objective code does not exist', function(v) {
  return ICRC.oc_validator_data && ICRC.oc_validator_data[v];
});
Validation.add('radar-validate-cost-center', 'The cost center does not exist', function(v) {
  return ICRC.cc_validator_data && ICRC.cc_validator_data[v];
});
Validation.add('radar-validate-unit-delegation-id', 'The unit or delegation does not exist', function(v, elm) {
  var reId = /^unit-delegation-selector-id-(.+)$/;
  var result = ICRC.unit_delegation_validator_data && ICRC.unit_delegation_validator_data[v];
  $w(elm.className).each(function(name) {
    var m = reId.exec(name);
    if (m) {
      if ($(m[1])) {
        if ($(m[1]).value == 'unit')
          result = ICRC.unit_validator_data && ICRC.unit_validator_data[v];
        if ($(m[1]).value == 'delegation')
          result = ICRC.delegation_validator_data && ICRC.delegation_validator_data[v];
      }
    }
  });
  return result;
});

