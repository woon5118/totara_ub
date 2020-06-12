<style lang="scss">
.tui-performElementEditDisplay {
  padding: var(--tui-gap-2) var(--tui-gap-3);
  background: var(--tui-color-neutral-2);
  border: var(--tui-border-width-thin) solid var(--tui-color-neutral-5);
  border-radius: var(--tui-border-radius-normal);
  cursor: pointer;

  &__inner {
    display: flex;
    flex-direction: column;
    &-header {
      display: flex;
      flex-direction: row;
      justify-content: space-between;
    }
  }
}
</style>
