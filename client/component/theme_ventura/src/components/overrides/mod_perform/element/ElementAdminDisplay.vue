<style lang="scss">
.tui-performElementEditDisplay {
  display: flex;
  flex-direction: column;
  padding: var(--tui-gap-2) var(--tui-gap-3);
  background: var(--tui-color-neutral-2);
  border: var(--tui-border-width-thin) solid var(--tui-color-neutral-5);
  border-radius: var(--tui-border-radius-normal);

  &__action {
    display: flex;
    flex-direction: row;
    justify-content: flex-end;
  }
  &__title {
    display: flex;
    flex-direction: row;
    margin-bottom: var(--tui-gap-3);
    @include tui-font-heading-x-small;

    &-response-required {
      @include tui-font-heading-label();
      margin-left: var(--tui-gap-1);
      color: var(--tui-color-prompt-alert);
    }
  }
  &__content {
    display: flex;
    flex-direction: row;
  }

  &__info {
    display: flex;
    flex-direction: row;
  }

  &__reportingId {
    &-header {
      margin: 0;
      @include tui-font-heading-label();
    }
    &-content {
      margin-top: var(--tui-gap-2);
    }
  }

  &__actions {
    padding: 0 var(--tui-gap-2);
    .tui-iconBtn {
      padding: 0;
    }
  }
}
</style>
