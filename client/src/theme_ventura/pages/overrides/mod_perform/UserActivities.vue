<style lang="scss">
.tui-performUserActivities {
  &__header-row {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    justify-content: space-between;
  }

  &__heading {
    margin: 0;
    padding-right: var(--tui-gap-8);
  }

  &__action-buttons {
    margin-top: var(--tui-gap-4);
  }

  &__content {
    margin-top: var(--tui-gap-4);
  }

  @include tui-font-body();
}

@media (min-width: $tui-screen-xs) {
  .tui-performUserActivities {
    &__action-buttons {
      margin-top: var(--tui-gap-4);
    }

    &__content {
      margin-top: var(--tui-gap-4);
    }
  }
}
</style>
