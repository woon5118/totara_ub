<style lang="scss">
.tui-performEditSectionContentAddElement {
  // Mobile Dropdown Width
  @media screen and (max-width: $tui-screen-sm) {
    width: 100%;
  }

  // Tablet/Small Desktop Dropdown Width
  @media screen and (min-width: $tui-screen-sm) and (max-width: $tui-screen-lg) {
    width: 50%;
  }

  // Desktop Dropdown Width
  @media screen and (min-width: $tui-screen-lg) {
    width: 30%;
  }

  & &__dropDownGroupItem {
    font-weight: bold;
    font-size: var(--tui-font-size-14);
    cursor: text;
    pointer-events: none;

    &:hover {
      background-color: var(--tui-dropdown-item-bg-color);
    }

    &:focus {
      background-color: var(--tui-dropdown-item-bg-color);
    }
  }

  & &__dropDownItem {
    padding-left: var(--tui-gap-8);
    font-size: var(--tui-font-size-14);
  }
}
</style>
