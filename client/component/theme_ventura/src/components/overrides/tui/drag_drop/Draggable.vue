<style lang="scss">
[data-tui-draggable] {
  &[data-animated] {
    transition: transform 0.25s;
  }

  &[data-dragging] {
    position: fixed;
    z-index: var(--tui-zindex-draggable);
    user-select: none;
    // required so droppable can get mouse events
    pointer-events: none;
  }
}
</style>
