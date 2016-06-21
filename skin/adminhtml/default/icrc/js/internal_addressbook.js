function addr_doForceRefreshGivenHelper(helper, element) {
  helper.changed = false;
  helper.hasFocus = true;
  helper.oldElementValue = element.value;
  helper.getUpdatedChoices();
}
