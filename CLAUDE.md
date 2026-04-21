# CLAUDE.md — Project Instructions for Claude Code

## 🧠 General Behavior
- Always read existing code before writing new code. Do not assume structure.
- Do not over-engineer. Prefer simple, readable solutions over clever ones.
- If unsure about intent, ask before implementing.
- Never delete or overwrite existing logic without explicit instruction.

---

## 🌿 Git Workflow
- Always work directly on `main` branch unless told otherwise.
- Do not create feature branches unless explicitly asked.
- Commit after each completed task with a clear, conventional commit message.
- Use conventional commits format **with scope**:
  - `feat(scope):` new feature
  - `fix(scope):` bug fix
  - `docs(scope):` documentation only
  - `refactor(scope):` code change, no feature/fix
  - `chore(scope):` tooling, config, migrations
- Scope must be specific to the area being changed, e.g:
  - `feat(dealer):`, `feat(appraisals):`, `feat(user):`
  - `fix(routes):`, `refactor(models):`, `docs(admin-flow):`
- One commit per task. Do not mix unrelated changes in a single commit.
