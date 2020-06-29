<style lang="scss">
.tui-performElementReadOnlyDisplay {
  padding: var(--tui-gap-2) var(--tui-gap-3);
  background: var(--tui-color-neutral-2);
  border: var(--tui-border-width-thin) solid var(--tui-color-neutral-5);
  border-radius: var(--tui-border-radius-normal);

  &__title {
    display: flex;
    flex-direction: row;
  }
  &__content {
    display: flex;
    flex-direction: row;
  }

  &__info {
    display: flex;
    flex-direction: row;
  }
  &__action {
    display: flex;
    align-items: end;
    justify-content: flex-end;
    width: 100%;
    margin-bottom: 0;
  }
}
</style>
